<style>
	/* html {
  background: #f5f5f5;
  font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
}
body {
  margin: 30px auto 0 auto;
  width: 450px;
  font-size: 75%;
} */

	h3 {
		margin-top: 30px;
		font-size: 18px;
		color: #555;
	}

	p {
		padding-left: 10px;
	}


	/*
 * Basic button style
 */
	.btn {
		box-shadow: 1px 1px 0 rgba(255, 255, 255, 0.5) inset;
		border-radius: 3px;
		border: 1px solid;
		display: inline-block;
		height: 18px;
		line-height: 18px;
		padding: 0 8px;
		position: relative;

		font-size: 12px;
		text-decoration: none;
		text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);
	}

	/*
 * Counter button style
 */
	.btn-counter {
		margin-right: 39px;
	}

	.btn-counter:after,
	.btn-counter:hover:after {
		text-shadow: none;
	}

	.btn-counter:after {
		border-radius: 3px;
		border: 1px solid #d3d3d3;
		background-color: #eee;
		padding: 0 8px;
		color: #777;
		content: attr(data-count);
		left: 100%;
		margin-left: 8px;
		margin-right: -13px;
		position: absolute;
		top: -1px;
	}

	.btn-counter:before {
		transform: rotate(45deg);
		filter: progid:DXImageTransform.Microsoft.Matrix(M11=0.7071067811865476, M12=-0.7071067811865475, M21=0.7071067811865475, M22=0.7071067811865476, sizingMethod='auto expand');

		background-color: #eee;
		border: 1px solid #d3d3d3;
		border-right: 0;
		border-top: 0;
		content: '';
		position: absolute;
		right: -13px;
		top: 5px;
		height: 6px;
		width: 6px;
		z-index: 1;
		zoom: 1;
	}

	/*
 * Custom styles
 */
	.btn {
		background-color: #dbdbdb;
		border-color: #bbb;
		color: #666;
	}

	.btn:hover,
	.btn.active {
		text-shadow: 0 1px 0 #b12f27;
		background-color: #f64136;
		border-color: #b12f27;
	}

	.btn:active {
		box-shadow: 0 0 5px 3px rgba(0, 0, 0, 0.2) inset;
	}

	.btn span {
		color: #f64136;
	}

	.btn:hover,
	.btn:hover span,
	.btn.active,
	.btn.active span {
		color: #eeeeee;
	}

	.btn:active span {
		color: #b12f27;
		text-shadow: 0 1px 0 rgba(255, 255, 255, 0.3);
	}
</style>
<div class="col-md-9 list-into-single">
	<div>
		<p class="list-page-single"><a href="<?= base_url() ?>">Home</a></p>>><p class="list-page-single"><a href="<?= base_url('/landing/blog/') . $blog['category'] ?>"><?= $blog['category'] ?></a></p>
	</div>
</div>
<div class="col-md-9 single-post-posts" style="padding-bottom: 20px">

	<div id="title-post">
		<h2><?= $blog['title'] ?></h2>
	</div>
	<div class="detail-post">
		<p class="date-post">
			<span class="glyphicon glyphicon-dashboard" style="margin-right:5px;color:rgb(28, 71, 128)"></span><b>Tanggal :</b>
			<span class="text-date-post"><?= format_date($blog['date'], 'd F Y') ?></span>
		</p>
		<p class="created-post" style="margin-right:10px">
			<span class="glyphicon glyphicon-user" style="margin-right:5px;color:rgb(28, 71, 128)"></span><b>Ditulis oleh : </b>
			<span class="text-created-post"><?= $blog['writer_name'] ?></span>
		</p>
		<p class="created-post">
			<span class="glyphicon glyphicon-star" style="margin-right:5px;color:rgb(28, 71, 128)"></span><b>Dilihat oleh : </b>
			<span class="text-created-post"><?= $blog['count_view'] ?></span>
		</p>
	</div>
	<?php if(UserModel::loginData('id', '-1') != '-1'): ?>
	<div class="detail-post">
		<p>
			<a href="#" title="Love it" class="btn btn-counter <?= $isLike ? 'active':''?>" data-count="<?= $countLike ?>" data-id="<?= $blog['id'] ?>"><span>&#x2764;</span></a>
		</p>
	</div>
	<?php endif; ?>
	<?php if (!empty($blog['attachment'])) : ?>
		<a href="<?= base_url() . 'uploads/' . $blog['attachment'] ?>">
			<button class="btn btn-primary">Lihat Lampiran</button>
		</a>
	<?php endif; ?>
	<?php if (!empty($blog['photo'])) : ?>
	<div id="img-post-wrap">
		<img class="img-responsive img-post" src="<?= asset_url($blog['photo']) ?>" />
	</div>
	<?php endif; ?>
	<p id="isi-post">
	<div style="text-align:justify">
		<?= $blog['body'] ?>
	</div>


	<br>
	<br>
	<br>

	</p>
	<div id="post-bottom-wrap">
		<div data-aos="zoom-up">
			<div id="terkait-post" class="col-sm-6">
				<h3>POST TERKAIT</h3>
				<div class="underscore" style="margin-left:0px;margin-left:0px;margin-bottom:15px;"></div>
				<ul id="terkait-post-list">
					<?php foreach ($blogTerkaits as $key => $blogTerkait) : ?>
						<li><a href="<?= base_url('landing/blog-view/' . $blogTerkait['id']) ?>"><?= $blogTerkait['title'] ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div data-aos="zoom-up">
			<div id="terbaru-post" class="col-sm-6">
				<h3>POST TEBARU</h3>
				<div class="underscore" style="margin-left:0px;margin-left:0px;margin-bottom:15px;"></div>
				<ul id="terbaru-post-list">
					<?php foreach ($blogTerbarus as $key => $blogTerbaru) : ?>
						<li><a href="<?= base_url('landing/blog-view/' . $blogTerbaru['id']) ?>"><?= $blogTerbaru['title'] ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</div>

</div>
<script>
	
	$('.btn-counter').on('click', function(event, count) {
		event.preventDefault();

		var $this = $(this),
			count = $this.attr('data-count'),
			active = $this.hasClass('active'),
			multiple = $this.hasClass('multiple-count');

		// First method, allows to add custom function
		// Use when you want to do an ajax request
		if (multiple) {
		$this.attr('data-count', ++count);
		// Your code here
		} else {
		$.ajax({
            type: "POST",
            url: document.head.querySelector('meta[name="base-url"]').content + "like/"+ (active ? "ajax_set_unlike" : "ajax_set_like"),
            data: {blogId: $this.attr('data-id')},
            cache: true,
            success: function (data) {
				if (data.message) {
					$this.attr('data-count', active ? --count : ++count).toggleClass('active');
				}
            },
            error: function (xhr, status, error) {
                alert(xhr.responseText + ' ' + status + ' ' + error);
            }
        });
		}

		// Second method, use when ... I dunno when but it looks cool and that's why it is here
		// $.fn.noop = $.noop;
		// $this.attr('data-count', !active || multiple ? ++count : --count)[multiple ? 'noop' : 'toggleClass']('active');

	});
</script>