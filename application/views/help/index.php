<article class="container">
    <h4 class="font-weight-bold">Help Page</h4>
    <p class="text-fade">For further information please contact us</p>

    <h5 class="mt-4 font-weight-bold">More Information</h5>

    <p>
        However if you are looking for more information then you can contact us through one of our preferred
        contact methods:
    </p>

    <ul class="mb-5">
        <li>Email: <?= $this->config->item('admin_email') ?></li>
        <li>By visiting this link: <a href="<?= site_url('help') ?>">Help Page</a></li>
    </ul>
</article>
<article class="container">
    <h5 class="font-weight-bold">Website Legal</h5>
    <p class="text-fade">Last updated 20 November 2020</p>

    <ul>
        <li><a href="<?= site_url('privacy') ?>">Privacy & Policy</a></li>
        <li><a href="<?= site_url('agreement') ?>">User Agreement</a></li>
        <li><a href="<?= site_url('cookie') ?>">Cookie Usage</a></li>
        <li><a href="<?= site_url('sla') ?>">SLA</a></li>
    </ul>
</article>
