<html>
<head>
	<style>
		@page {
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
		.page{
			page-break-after: always;
		}

		ol {
			counter-reset: item
		}

		li {
			display: block
		}

		li:before { content: counters(item, ".") " "; counter-increment: item
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
		<table cellspacing="0" cellpadding="0" width='100%' border='1'>
			<thead>
				<tr>
					<th>No</th>
					<th>Kode Aset</th>
					<th>Nama Aset</th>
					<th>Unit PJ Barang</th>
					<th>Nilai Perolehan (Rp)</th>
					<th>Nilai Penyusutan (Rp)</th>
					<th>Akumulasi Penyusutan (Rp)</th>
					<th>Nilai Buku</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if($content){
					$i=1;
					foreach ($content as $key => $row) {
						
				?>
				<tr>
					<td><?php echo $i++ ?></td>
					<td><?php echo $row['kode_aset']?></td>
					<td><?php echo $row['nama_aset']?></td>
					<td><?php echo $row['unitkerjaNama']?></td>
					<td><?php echo number_format($row['mstPenystnNilaiPerolehan'],0,",",".")?></td>
					<td><?php echo number_format($row['nilai_penyusutan'],0,",",".")?></td>
					<td><?php echo number_format($row['total_penyusutan'],0,",",".")?></td>
					<td><?php echo number_format($row['nilai_buku'],0,",",".")?></td>
				</tr>
				<?php 
					}
				}
				?>
			</tbody>
		</table>
	</div>
</body>
</html>