var enableWebcamButton = document.getElementById('webcamButton');
var captureButton = document.getElementById('capture');
var saveImageButton = document.getElementById('save');
var disableButton = document.getElementById('closePopup');
var video = document.getElementById('webcam');
var profileImage = []
// var webcamPopup = document.getElementById('webcamPopup')
var loader = document.getElementById('loader')
var model = undefined;
// var canvas = document.getElementById("canvas");
// var ctx = canvas.getContext("2d");
const uploadInput = document.getElementById('photo');
const imagePreview = document.getElementById('imagePreview');
const liveView = document.getElementById('liveView');

document.addEventListener('DOMContentLoaded', async () => {
	// Load the blazeface model
	model = await blazeface.load();
});

// var faceDetectionInterval
enableWebcamButton.addEventListener('click', function (event) {
	console.log(event, "clicked")
	accessCamera();
	// webcamPopup.style.display = 'block';
});

captureButton.addEventListener('click', async () => {
	console.log(video.srcObject)

	if (captureButton.innerText == "Retake") {
		video.play();
		captureButton.innerText = 'Capture'
	} else {
		video.pause();
		captureButton.innerText = 'Retake'

	}


});

saveImageButton.addEventListener('click', async (e) => {
	e.preventDefault();
	e.stopPropagation();
	imagePreview.style.display = 'block';
	const canvas = document.createElement('canvas');
	const ctx = canvas.getContext('2d');
	// Set canvas dimensions to match video dimensions
	console.log(video.videoWidth, video.videoWidth)
	canvas.width = video.videoWidth;
	canvas.height = video.videoHeight;
	ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

	// Set the preview image source
	dataurl = canvas.toDataURL('image/png');
	imagePreview.src = await canvas.toDataURL('image/png');
	let selectedFile = await dataURLtoFile(dataurl, 'captured.png')
	profileImage[0] = selectedFile;



	disableButton.click()
})
function dataURLtoFile(dataurl, filename) {
	var arr = dataurl.split(','),
		mime = arr[0].match(/:(.*?);/)[1],
		bstr = atob(arr[arr.length - 1]),
		n = bstr.length,
		u8arr = new Uint8Array(n);
	while (n--) {
		u8arr[n] = bstr.charCodeAt(n);
	}
	return new File([u8arr], filename, { type: mime });
}


uploadInput.addEventListener('change', async function (event) {
	console.log("click", event.target.files)
	if (uploadInput.files.length > 0) {
		const selectedFile = event.target.files[0];
		profileImage[0] = selectedFile;
	}
});


const accessCamera = () => {
	// webcamPopup.style.display = 'block';
	loader.style.display = 'block'; // Show loader when accessing camera
	captureButton.innerText = 'Capture'

	navigator.mediaDevices
		.getUserMedia({
			video: { width: 320, height: 400 },
			audio: false,
		})
		.then((stream) => {
			video.srcObject = stream;
		}).catch(error => {
			console.error('Error accessing camera:', error);
			loader.style.display = 'none'; // Hide loader on error

		});
};
const disableCamera = () => {
	loader.style.display = 'none'; // Hide loader when stopping the camera
	// webcamPopup.style.display = 'none';
	if (video.srcObject) {
		const tracks = video.srcObject.getTracks();
		tracks.forEach((track) => track.stop());
	}
};
var children = [];


const detectFaces = async () => {
	const prediction = await model.estimateFaces(video, false);
	if (prediction.length === 0 || prediction.length > 1) {
		const p = document.createElement('p');

		const errorMessage = prediction.length ? "Multiple faces detected!" : "No face detected!"
		p.innerText = errorMessage;
		p.style = "font:24px Arial; position: absolute;left: 0;right: 0;margin: 0 auto;bottom: 50%; text-align:center; color:red"
		liveView.appendChild(p);
		children.push(p);
		saveImageButton.disabled = true
		captureButton.disabled = true

	} else {
		for (let i = 0; i < children.length; i++) {
			liveView.removeChild(children[i]);
		}
		children.splice(0);
		saveImageButton.disabled = false
		captureButton.disabled = false
		// prediction.forEach((predictions) => {

		// const x = predictions.topLeft[0];
		// const y = predictions.topLeft[1];

		// const width = predictions.bottomRight[0] - predictions.topLeft[0];
		// const height = predictions.bottomRight[1] - predictions.topLeft[1];

		// // Drawing rectangle that'll detect the face
		// ctx.beginPath();
		// ctx.lineWidth = "1";
		// ctx.strokeStyle = "green";
		// ctx.rect(x, y, height, width);
		// ctx.stroke();
		// });
	}
};
disableButton.addEventListener('click', disableCamera);

video.addEventListener("loadeddata", async () => {

	model = await blazeface.load();
	loader.style.display = 'none'; // Hide loader once the model is loaded

	// Calling the detectFaces 40 times per second
	setInterval(detectFaces, 40);
});



document.getElementById("uploadPhoto").addEventListener('click', async () => {
	imagePreview.style.display = 'block';
	// Set the preview image source
	// imagePreview.src = canvas.toDataURL('image/png');

	if (profileImage.length > 0) {
		let selectedFile = profileImage[0];
		const newImage = new Image();

		(async () => {
			newImage.src = URL.createObjectURL(selectedFile);

			const waitUntilImageLoaded = () => {
				return new Promise((resolve, reject) => {
					newImage.onload = () => resolve();
					newImage.onerror = reject;
				});
			};


			try {
				await waitUntilImageLoaded();
				// Now the image is fully loaded, proceed with face detection
				const predictions = await model.estimateFaces(newImage);
				if (predictions.length === 0 || predictions.length > 1) {
					imagePreview.style.display = 'none';
					profileImage = []

					alert('No faces detected in the selected image.');
					return; // Stop the script execution
				}
				imagePreview.src = URL.createObjectURL(selectedFile);
			} catch (error) {
				// Handle any errors during image loading
				console.error('Error loading image:', error);
			}
		})();


	} else {
		imagePreview.style.display = 'none';

		alert("please upload image")

	}

})