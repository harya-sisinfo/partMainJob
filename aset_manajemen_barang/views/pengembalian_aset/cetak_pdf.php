<html>
<head>
	<style>
		.page{
			size: A4;
			margin: 0.5cm;
			margin-left: 1cm;
			margin-right: 1cm;
			font-family: verdana, sans-serif;
			font-size: small;
			
		}
		.font-small {
			font-size: x-small;
		}

		
		table{
            border-collapse: collapse;
            font-size: 14px;
            border-spacing: 2px;
        }
		th{
			text-align: center;
			font-weight: bold;
			padding: 3px;
		}
		td{
			padding: 3px;
		}

	</style>
</head>
<body onload="window.print()">
	<div class="page">
		<table class="font-small" width="100%" border="0" cellspacing="0" cellpadding="1px">
			<tbody>
				<tr>
					<td width="60px" valign="top">
						<img src="<?php echo $logo ?>" height="50" width="50"/>
					</td>
					<td width="60%" valign="top">
						Universitas Gadjah Mada
						<br/>Bulaksumur Caturtunggal,Depok Kab.Sleman
						<br/>Daerah Istimewa Yogyakarta

					</td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
			</tbody>
		</table>
		<h2 style="text-align: center;"><strong><?php echo $header?></strong></h2>
		<table class="font-small"  border="0" cellspacing="0" cellpadding="1px">
			<tr>
				<td valign="top">
					Nomor Pengembalian
				</td>
				<td valign="top">
					:&nbsp;&nbsp;<?php echo $kembali_nomor?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Tanggal Pengembalian
				</td>
				<td valign="top">
					:&nbsp;&nbsp;<?php echo $kembali_tanggal?>
				</td>
			</tr>
			<tr>
				<td valign="top">
					Keterangan
				</td>
				<td valign="top">
					:&nbsp;&nbsp;<?php echo $kembali_keterangan?>
				</td>
			</tr>
		</table>
		<p><p>
		<h3 style="text-align: left;">List Item</strong></h3>
		<table cellspacing="0" cellpadding="0" width='100%' border='1'>
			<thead>
				<tr>
					<th>No</th>
					<th>Kode Sewa</th>
					<th>Label</th>
					<th>Rusak (Rp)</th>
					<th>Ganti (Rp)</th>
					<th>Keterangan</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if($content){
					$i=1;
					foreach ($content as $key => $row) {
						
				?>
				<tr>
					<td><?php echo $i++?></td>
					<td><?php echo $row['kembali_detail_kode']?></td>
					<td><?php echo $row['kembali_detail_barang']?></td>
					<td align="right"><?php echo number_format($row['kembali_detail_rusak'],0,",",".")?></td>
					<td  align="right"><?php echo number_format($row['kembali_detail_ganti'],0,",",".")?></td>
					<td><?php echo $row['kembali_detail_keterangan']?></td>
				</tr>
				<?php 
					}
				}
				?>
			</tbody>
		</table>
		<br>
		<br>
		<br>
		<table class="font-small" width="100%"  border="0" cellspacing="0" cellpadding="1px">
			<tr>
				<td valign="top">
				</td>
				<td valign="top" >
					Yogyakarta, <?php echo tgl_indo($this->date_today)?>
				</td>
			</tr>
			<tr>
				<td valign="top" height="100px" width="70%">
				</td>
				<td valign="top">
					
				</td>
			</tr>
			<tr>
				<td valign="top">
				</td>
				<td valign="top" align="center">
					<?php echo $kembali_nama?>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>