<form class="form-plaintext">
    <h5 class="font-weight-bold mb-3">Data History</h5>
    <?php foreach ($statusHistory['data'] as $title => $datum): ?>
        <?php if(is_array($datum)): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">
                        <?= strtoupper(str_replace(['-', '_'], ' ', $title)) ?>
                    </h5>
                    <div class="row">
                        <?php foreach ($datum as $key => $value): ?>
                            <?php if(is_array($value)): ?>
                                <div class="col-12">
                                    <div class="card grid-margin">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <?= strtoupper(str_replace(['-', '_'], ' ', (is_numeric($key) ? 'Data' . ($key + 1) : $key))) ?>
                                            </h6>
                                            <div class="row">
                                                <?php foreach ($value as $innerKey => $innerValue): ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group row">
                                                            <label class="col-sm-4 col-form-label">
                                                                <?= ucwords(str_replace(['-', '_'], ' ', $innerKey)) ?>
                                                            </label>
                                                            <div class="col-sm-8">
                                                                <p class="form-control-plaintext">
                                                                    <?= if_empty(is_numeric($innerValue) ? numerical($innerValue) : $innerValue, '-') ?>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-sm-4 col-form-label">
                                            <?= ucwords(str_replace(['-', '_'], ' ', $key)) ?>
                                        </label>
                                        <div class="col-sm-8">
                                            <p class="form-control-plaintext">
                                                <?= if_empty(is_numeric($value) ? numerical($value) : $value, '-') ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">
                                    <?= strtoupper(str_replace(['-', '_'], ' ', $title)) ?>
                                </label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext">
                                        <?= $datum ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</form>
