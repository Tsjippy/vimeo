import * as tus from 'tus-js-client';
import { fetchRestApi } from './../../tsjippy-shared-functionality/includes/js/partials/form_submit_functions.js';

console.log('vimeo upload loaded');

export class VimeoUpload{
    constructor(file){
        this.urlStorage     = tus.defaultOptions.urlStorage;
        this.getFingerprint = tus.defaultOptions.fingerprint;
        this.file           = file;
        this.storedEntry    = {};
    }

    async findInStorage(){
        this.fingerprint    = await this.getFingerprint(this.file, { endpoint: tsjippy.baseUrl });
        let storedEntries   = await this.urlStorage.findUploadsByFingerprint(this.fingerprint);

        if (storedEntries.length) {
            this.storedEntry = storedEntries[0];
            if (this.storedEntry.uploadUrl) {
                return true;
            }
            // cleanup
            this.urlStorage.removeUpload(this.storedEntry.urlStorageKey);
        }

        return false;
    }

    async getVimeoUploadUrl(){
        var formData = new FormData();
        formData.append('file-size', this.file.size);
        formData.append('file-name', this.file.name);
        formData.append('file-type', this.file.type);

        var response    = await fetchRestApi('vimeo/prepare_vimeo_upload', formData);

        //Failed
        if(response){
            var uploadUrl		= response.upload_link;
            var postId		    = response['post-id'];
            var vimeoId		    = response.vimeo_id;

            this.storedEntry = {
                size: this.file.size,
                metadata: {
                    filename: this.file.name,
                    filetype: this.file.type,
                },
                creationTime: new Date().toString(),
                url: uploadUrl,
                postId: postId,
                vimeoId: vimeoId
            };

            this.storedEntry.urlStorageKey = await this.urlStorage.addUpload(this.fingerprint, this.storedEntry);

            return true;
        }else{
            console.error('Failed');
            console.error(formData);
            console.log(this.file);

            // reset
            return false;
        }
    }

    async tusUploader(){
        /* 
        ** Get the upload url
        */
        // Check if already an url in memory
        var existing    = await this.findInStorage();
        if(!existing){
            // Nothing found, get a new upload url
            var result = await this.getVimeoUploadUrl();

            if(!result){
                return false;
            }
        }

        return new tus.Upload(this.file, {
            uploadUrl: this.storedEntry.url,
            headers: {
                // https://developer.vimeo.com/api/upload/videos#resumable-approach-step-2
                Accept: 'application/vnd.vimeo.*+json;version=3.4' // required
            },
            chunkSize: 50000000, // required
        });
    }

    removeFromStorage(){
        this.urlStorage.removeUpload(this.storedEntry.urlStorageKey);
    }
}

async function uploadVideo(file){
	var uploader	= new vimeoUploader.VimeoUpload(file);
	var upload		= await uploader.tusUploader();
	var s			= '';

	// Could not upload
	if(!upload){
		//clear file upload
		fileUploadWrap.querySelector('.file-upload').value = "";
				
		//Remove progress barr
		document.getElementById("progress-wrapper").remove();

		// Hide the loader
		document.querySelector('.loader-wrapper:not(.hidden)').classList.add('hidden');

		return false;
	}

	// Upload started
	if (totalFiles > 1){
		s = "s";
	}else{
		s = "";
	}

	upload.options.onProgress   = function(bytesUploaded, bytesTotal) {
		//calculate percentage
		var percentage = (bytesUploaded / bytesTotal * 100).toFixed(2)
	
		//show percentage in progressbar
		document.getElementById("upload-progress").value			= percentage;
		document.getElementById("progress-percentage").textContent	= `   ${percentage}%`;

		if(percentage>98){
			document.querySelector('.upload-message').textContent = "Processing video"+s;
			document.getElementById('progress-wrapper').classList.add('hidden');
		}
	};

	upload.options.onSuccess    = async function() {
		let postId	= uploader.storedEntry.postId;
		// Add post id of the video to the form
		let formData = new FormData();
        formData.append('post-id', postId);
    
        let request = new XMLHttpRequest();
        request.open('POST', `${tsjippy.baseUrl}/wp-json${tsjippy.restApiPrefix}/vimeo/add_uploaded_vimeo`, false);
        request.send(formData);

		//Remove progress barr
		document.getElementById("progress-wrapper").remove();
		
		let link	= `
		<div class="vimeo-wrapper">
			<div class='vimeo-embed-container loading'>
				<iframe src='https://player.vimeo.com/video/${uploader.storedEntry.vimeoId}' frameborder='0' webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe><br>
			</div>
		</div>`;

		var preview		= addPreview(link, postId);

		// Hide the loader
		document.querySelector('.loader-wrapper:not(.hidden)').classList.add('hidden');

		Main.displayMessage(`The file ${file.name} has been uploaded succesfully.`, 'success', 1500);
		
		//Hide upload button if only one file allowed
		if(!fileUploadWrap.querySelector('.file-upload').multiple){
			fileUploadWrap.querySelector('.upload-div').classList.add('hidden');
		}

		//clear file upload
		fileUploadWrap.querySelector('.file-upload').value = "";
		
		uploader.urlStorage.removeUpload(uploader.storedEntry.urlStorageKey);

		// check if we are uploading from frontend posting form
		var postForm = document.getElementById('postform');
		if(postForm != null){
			postForm.querySelector('[name="update"]').value		= 1;
			postForm.querySelector('[name="post-id"]').value	= postId;
		}

		// Wait for the video to be processed on Vimeo
		var result	= '';
		while(!result.ok){
			await new Promise(res => setTimeout(res, 10000));
			result = await fetch(
				`https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/${uploader.storedEntry.vimeoId}`,
				{method: 'GET'}
			);
		}
		var response	= await result.json();

		preview.querySelector('.vimeo-wrapper').innerHTML = DOMPurify.sanitize(response.html);
	}

	upload.options.onError      = function(error) {
		console.error("Failed because: " + error);				
	}

	document.querySelector('.upload-message').textContent = "Uploading video"+s+" to Vimeo";
	document.getElementById('progress-wrapper').classList.remove('hidden');

	upload.start();
}

document.addEventListener("DOMContentLoaded", () => {
    FileUpload.fileTypeFilter['video'] = function(){
        createProgressBar(target);

        //update post id on a postform
        await uploadVideo(file);
    }
});