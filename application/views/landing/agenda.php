<div class="col-md-9 list-into-single">
	<div>
		<p class="list-page-single"><a href="<?= base_url() ?>">Home</a></p>>><p class="list-page-single">Agenda</p>
	</div>
</div>
<div class="col-md-9 single-post-posts" style="padding-bottom: 20px">

	<div id="title-post">
		<h2><?= $agenda['title'] ?></h2>
	</div>
	<div class="detail-post">
		<p class="date-post">
			<span class="glyphicon glyphicon-dashboard" style="margin-right:5px;color:rgb(28, 71, 128)"></span><b>Tanggal Agenda :</b>
			<span class="text-date-post"><?= format_date($agenda['date'], 'd F Y') ?></span>
		</p>
	</div>
	<div id="img-post-wrap">
		<img class="img-responsive img-post" src="<?= asset_url($agenda['photo']) ?>" />
	</div>
	<p id="isi-post">
	<div style="text-align:justify">
		<?= $agenda['content'] ?>
	</div>


	<br>
	<br>
	<br>

	</p>

</div>