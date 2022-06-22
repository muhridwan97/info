  <div class="col-sm-12" style="padding-bottom: 40px">
    <div class="container">
      <div class="col-sm-8">
        <h2 class="title-section" style="text-align: left;">POST TERBARU</h2>
        <div class="underscore" style="margin-left:0px"></div>
      <?php foreach ($newBlogs as $key => $post) : ?>
        <div class="col-sm-12" style="padding:0px;border-bottom: 1px solid #bcccec;padding-bottom: 20px;margin-bottom: 30px">
          <div class="custom-entry">
            <div class="entry-month">
              <?= format_date($post['date'], 'F') ?> </div>
            <div class="entry-date">
              <?= format_date($post['date'], 'd') ?> </div>
            <div class="entry-month">
              <?= format_date($post['date'], 'Y') ?> </div>
          </div>
          <a href="<?= base_url('landing/blog-view/' . $post['id']) ?>">
            <h3 class="title-post-popular" style="margin-top:0px"><?= $post['title'] ?></h3>
          </a>
          <h6 style="color:#555;font-family: myf">Penulis : <b><?= $post['writer_name'] ?></b></h6>

          <p class="content-popular-post">
            <?= substr(strip_tags($post['body']), 0, 110) . "..." ?> </p>
        </div>
      <?php endforeach; ?>
      </div>
      <div class="col-sm-4">
        <h3 class="title-section" style="text-align: left;">POST POPULER</h3>
        <div class="underscore" style="margin-left:0px"></div>
        <div id="post-populer-sidebar">
          <ul id="post-populer-sidebar-list">
          <?php foreach ($blogPopulars as $key => $blogPopular) : ?>
            <li><a href="<?= base_url('/landing/blog-view/' . $blogPopular['id']) ?>"><span class="glyphicon glyphicon-file" style="margin-right:5px"></span><?= $blogPopular['title'] ?></a></li>
          <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </div>