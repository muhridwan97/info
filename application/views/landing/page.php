<div class="col-md-9 list-into-single">
	<div>
		<p class="list-page-single"><a href="<?= base_url()?>">Home</a></p>>><p class="list-page-single"><a href="#"><?= $content['page_name']; ?></a></p>
	</div>
</div>
<div class="col-md-9 single-post-posts">

	<div id="title-list-posts-wrap">
		<h2 class="title-section" style="text-align:left"><?= $content['page_name']; ?></h2>
		<div class="underscore" style="margin-left:0px;margin-left:0px;margin-bottom:15px;"></div>
	</div>
	<br />
	<div class="col-md-9">
		<div id="content-center">
			<div class="article-content">
				<div class="entry-content">
					<?= $content['content']; ?>
				</div>
			</div>
		</div>
	</div>

</div>