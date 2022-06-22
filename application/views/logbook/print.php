<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>Bukti bimbingan</title>
	<link rel="stylesheet" href="<?= base_url(get_asset('vendors.css')) ?>">
	<link rel="stylesheet" href="<?= base_url(get_asset('app.css')) ?>">
    <style>
        @page {
            margin: 40px;
        }

        body {
            margin: 10px;
            margin-left: 30px;
            margin-right: 30px;
            background: none;
        }

        p {
            font-family: "Times New Roman";
            color:black;
        }

        .table-consultation{
            background-image: url(<?php echo base_url("assets/dist/img/uin.png");?>);
        }
    </style>
</head>
<body>

<p style="margin-left: 10px;font-size: 11px; margin-bottom: 0; line-height: 1.1; text-align:center;">
    <strong><u>KARTU BIMBINGAN SKRIPSI/TUGAS AKHIR</u></strong>
</p>

<table style="margin-left: 10px;margin-top: 15px;font-size: 14px; margin-bottom: 5px;">
    <tbody>
    <tr style="vertical-align:top">
        <th><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            Nama Mahasiswa
            </p>
        </th>
        <th style="text-align: right;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                :
            </p>
        </th>
        <th style="text-align: left;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                <?= $skripsi['nama_mahasiswa'] ?>
            </p>
        </th>
    </tr>
    <tr style="vertical-align:top">
        <th><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            NIM
            </p>
        </th>
        <th style="text-align: right;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                :
            </p>
        </th>
        <th style="text-align: left;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                <?= $skripsi['no_student'] ?>
            </p>
        </th>
    </tr>
    <tr style="vertical-align:top">
        <th><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            Pembimbing
            </p>
        </th>
        <th style="text-align: right;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                :
            </p>
        </th>
        <th style="text-align: left;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                <?= $skripsi['nama_pembimbing'] ?>
            </p>
        </th>
    </tr>
    <tr style="vertical-align:top">
        <th><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            Judul
            </p>
        </th>
        <th style="text-align: right;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                :
            </p>
        </th>
        <th style="text-align: left;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                <?= $skripsi['judul'] ?>
            </p>
        </th>
    </tr>
    <tr style="vertical-align:top">
        <th><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            Fakultas
            </p>
        </th>
        <th style="text-align: right;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                :
            </p>
        </th>
        <th style="text-align: left;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
             Ilmu Tarbiyah dan Keguruan UIN Sunan Kalijaga Yogyakarta
            </p>
        </th>
    </tr>
    <tr style="vertical-align:top">
        <th><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            Program Studi
            </p>
        </th>
        <th style="text-align: right;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                :
            </p>
        </th>
        <th style="text-align: left;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                Pendidikan Fisika
            </p>
        </th>
    </tr>
    </tbody>
</table>

<table class="table-consultation" style="margin-left: 10px;margin-top: 10px;font-size: 14px; margin-bottom: 10px;border: 1px solid black;width: 100%;background-repeat: no-repeat;background-position: center center;">
    <tbody>
    <tr style="background-color:#CCCCCC">
        <th style="text-align: center;border: 1px solid black;width: 30px"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            <strong>No.</strong>
            </p>
        </th>
        <th style="text-align: center;border: 1px solid black;width: 100px"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
               Tanggal
            </p>
        </th>
        <th style="text-align: center;border: 1px solid black;width: 80px"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            Konsultasi ke :
            </p>
        </th>
        <th style="text-align: center;border: 1px solid black;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            Materi Bimbingan
            </p>
        </th>
        <th style="text-align: center;border: 1px solid black;width: 100px"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            Tanda tangan pembimbing
            </p>
        </th>
    </tr>
    <?php foreach ($logbooks as $key => $logbook) : ?>
    <tr>
        <td style="text-align: center;border: 1px solid black;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
                <?=$key+1 ?>
            </p>
        </td>
        <td style="text-align: center;border: 1px solid black;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            <?= format_date($logbook['tanggal'],'d-m-Y');?>
            </p>
        </td>
        <td style="text-align: center;border: 1px solid black;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            <?=$key+1 ?>
            </p>
        </td>
        <td style="text-align: left;border: 1px solid black;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            <?= $logbook['description'];?>
            </p>
        </td>
        <td style="text-align: center;border: 1px solid black;"><p style="margin-bottom: 5px; line-height: 1.3; font-size: 14px;font-weight: normal;">
            <img src="data:image/png;base64,<?= $qrCodePembimbing ?>" style="width: 20px; height: 20px;">
            </p>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<table style="margin-left:160px;font-size: 14px; margin-top: 40px; margin-bottom: 10px; width: 100%">
    <tbody>
    <tr>
        <th style="width: 100px;"></th>
        <th style="width: 100px;"></th>
        <th style="text-align: left;"><p style="line-height: 1.3;margin-bottom: 5px;font-size: 14px;font-weight: normal;">Yogyakarta, <?= $tanggalSekarang ?></p>
            <p style="line-height: 1.3;margin-bottom: 5px;font-size: 14px;font-weight: normal;">Pembimbing</p>
            </th>
    </tr>
    <tr>
        <th style="width: 100px;"></th>
        <th style="width: 100px;"></th>
        <th style="text-align: left;"><img src="data:image/png;base64,<?= $qrCodePembimbing ?>"></th>
    </tr>
    <tr>
        
        <th style="width: 100px;"></th>
        <th style="width: 100px;"></th>
        <th style="text-align: left;"><p style="line-height: 1.3;margin-bottom: 5px;font-size: 14px;font-weight: normal;"><u><?= $skripsi['nama_pembimbing'] ?></u></p>
        <p style="line-height: 1.3;margin-bottom: 5px;font-size: 14px;font-weight: normal;">NIP. <?= $skripsi['no_lecturer'] ?></p></th>
    </tr>
    </tbody>
</table>

<script type="text/php">
    $x = 280;
    $y = 810;
    $text = "{PAGE_NUM} of {PAGE_COUNT}";
    $font = $fontMetrics->get_font("helvetica", "bold");
    $size = 10;
    $color = array(.08, .08, .08);
    $word_space = 0.0;  //  default
    $char_space = 0.0;  //  default
    $angle = 0.0;   //  default
    $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
</script>
</body>
</html>
