<div class="col-md-9 list-into-single">
	<div>
		<p class="list-page-single"><a href="<?= base_url()?>">Beranda</a></p>
		<p class="list-page-single">>> <a href="#">Search</a></p>
	</div>
</div>
<div class="col-md-9 single-post-posts">

	<div id="title-list-posts-wrap">
		<h2 class="title-section" style="text-align:left">Hasil Pencarian : "<?= $search ?>"</h2>
		<div class="underscore" style="margin-left:0px;margin-right:0px;"></div>
	</div>

	<?php foreach ($data as $row) :?>
		<div class="panel-post-wrap">
			<div class="col-sm-4 img-list-posts-wrap">
				<img src="<?= asset_url($row['photo']) ?>" class="img-responsive img-list-posts" />
			</div>
			<div class="col-sm-8">
				<h3 class="title-isi-list-posts"><a href="<?= base_url('/landing/blog-view/' . $row['id']) ?>"><?= $row['title'] ?></a></h3>
				<div class="detail-post detail-post-list-posts">
					<p class="date-post">
						<span class="glyphicon glyphicon-dashboard" style="margin-right:5px;color:#29CC6D"></span><b>Tanggal :</b>
						<span class="text-date-post"><?= format_date($row['date'], 'd F Y')?></span>
					</p>
					<p class="created-post">
						<span class="glyphicon glyphicon-user" style="margin-right:5px;color:#29CC6D"></span><b>Ditulis oleh : </b>
						<span class="text-created-post"><?= $row['writer_name'] ?></span>
					</p>
				</div>
				<div class="isi-lists-posts">
					<p>
					<?= substr(strip_tags($row['body']), 0, 110) . "..." ?> </p>
				</div>
				<a href="<?= base_url('/landing/blog-view/' . $row['id']) ?>">
					<button type="button" class="btn btn-success">Read More</button>
				</a>
			</div>
		</div>
	<?php endforeach; ?>

	<div class="col-sm-12 pagination-wrap">
		<?php echo $pagination; ?>
	</div>
</div>