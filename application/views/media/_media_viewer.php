<div id="media-viewer-wrapper">
	<?php $fileType = get_file_type($source, $mime ?? null) ?>
	<?php if ($fileType == 'image'): ?>
		<div class="text-center border" style="width:100%; height:100vh; overflow-y: auto; overflow-x: hidden">
			<img src="<?= asset_url($source) ?>" class="img-fluid" alt="<?= $title ?? 'Source' ?>">
		</div>
	<?php elseif ($fileType == 'pdf'): ?>
		<!-- using pdf.js by mozilla: -->
		<iframe src="<?= base_url('assets/dist/lib/Pdf.js/web/viewer.html?file=' . asset_url($source)) ?>" allowfullscreen
				type="application/pdf" style="width:100%; height:100vh;" class="border"></iframe>
		<!-- using ViewerJS library: <iframe src="<?= base_url('assets/dist/lib/ViewerJS/#' . asset_url($source) . '?//' . url_title($title ?? 'Source')) ?>"
				style="width:100%; height: 100vh" class="border" id="document-viewer" allowfullscreen></iframe> -->
		<!-- built-in browser viewer: <iframe src="<?= asset_url($source) ?>#toolbar=0" allowfullscreen
			type="application/pdf" style="width:100%; height:100vh;" class="border"></iframe> -->
	<?php elseif ($fileType == 'video'): ?>
		<!-- remove video-js class to using built-in browser video player -->
		<video class="border-0 video-js vjs-default-skin vjs-big-play-centered" id="app-video-player" width="100%" controls preload="auto" oncontextmenu="return false;">
			<source src="<?= asset_url($source) ?>" type="<?= if_empty($mime ?? null, 'video/mp4') ?>">
			Your browser does not support the video tag.
		</video>
	<?php elseif (in_array($fileType, ['presentation', 'document', 'excel', 'text'])): ?>
		<div class="alert alert-danger mb-2" id="google-viewer-not-responding" style="display: none">
			<i class="mdi mdi-presentation-play mr-2"></i>Viewer is not responding, consider open the source directly by
			<a href="https://docs.google.com/viewer?url=<?= asset_url($source, false, env('S3_BUCKET')) ?>&embedded=true">
				click this link <i class="mdi mdi-open-in-app"></i>
			</a>
		</div>
		<div class="position-relative bg-light">
			<i class="mdi mdi-loading mdi-spin h1 position-absolute" style="left: 50%; top: 50%; transform: translate(-50%, -50%);"></i>
			<iframe src="https://docs.google.com/viewer?url=<?= asset_url($source, false, env('S3_BUCKET')) ?>&embedded=true" allowfullscreen
				style="width:100%; height:100vh;" id="google-viewer" class="border-0 position-relative"></iframe>
			<div class="position-absolute" style="width: 50px; height: 45px; right: 10px; top: 10px"></div>
		</div>
	<?php else: ?>
		<div class="alert alert-warning">
			<h6 class="mb-1 font-weight-bold">Unknown Media Type</h6>
			<i class="mdi mdi-alert-outline mr-1"></i>
			Cannot showing the media please download the source file instead.
		</div>
		<a href="<?= asset_url($source) ?>" class="btn btn-primary">
			Download Media <i class="mdi mdi-download"></i>
		</a>
	<?php endif; ?>
</div>
