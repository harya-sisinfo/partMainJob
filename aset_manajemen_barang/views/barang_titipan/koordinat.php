<?php echo Modules::run('breadcrump'); ?>
<style>
#mapid { height: 400px; }
</style>
<link rel="stylesheet" href="<?php echo base_url('ugmfw-assets/plugins/leaflet/leaflet.css'); ?>" />
<script src="<?php echo base_url('ugmfw-assets/plugins/leaflet/leaflet.js'); ?>"></script>

<script>
$(document).ready(function(){
    let basepath = '<?php echo base_url('ugmfw-assets/plugins/leaflet'); ?>';
    let accessToken = 'pk.eyJ1IjoidG9waWtlY2hlIiwiYSI6ImNqd2JvOHNxMTBqbmM0MW81bTdqejc3bmUifQ.gUeNzdKFIfndyPBy6qmhnQ';

    var defaultIcon = L.Icon.extend({
        options:{
            shadowUrl: basepath+'/images/marker-shadow.png',
            iconSize:[25,41],
            iconAnchor:[12,41],
            popupAnchor:[1,-34],
            tooltipAnchor:[16,-28],
            shadowSize:[41,41]
        }
    });
    var myIcon = new defaultIcon({iconUrl: basepath+'/images/marker-icon.png'});
 

    var mymap = L.map('mapid').setView([-7.77031, 110.37778], 15);

    L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token='+accessToken, {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox/streets-v11',
        tileSize: 512,
        zoomOffset: -1,
        accessToken: accessToken
    }).addTo(mymap);

    var marker = L.marker([-7.77341, 110.37651]).addTo(mymap);
    

    let markerGroup = L.layerGroup().addTo(mymap);
    L.marker([-7.76890, 110.38307], {icon: myIcon}).addTo(markerGroup);
    mymap.on('click', function(e) {
        markerGroup.clearLayers();
        console.log("Lat, Lon : ", e.latlng.lat, e.latlng.lng)
        new L.marker(e.latlng).addTo(markerGroup);
    });

    setTimeout(function() {
        mymap.invalidateSize()
    }, 100);

});
</script>

<table style="clear: both" class="table table-bordered table-striped">
    <tbody>
        <?php //echo "<pre>"; print_r($detailAset); echo "</pre>"; ?>
        <tr>
        	<td style="width: 20%;"><b>Kode Aset</b></td>
        	<td><b><?php echo $detailAset->invDetKodeBarang; ?></b></td>
        </tr>
    
        <tr>
        	<td><b>Label Aset</b></td>
        	<td><?php echo $detailAset->invDetMstLabel; ?></td>
        </tr>
        
        <tr>
        	<td><b>Merk</b></td>
        	<td><?php echo $detailAset->invDetMerek; ?></b>
            </td>
        </tr>
        
        <tr>
        	<td colspan="2" style="text-align: center;">
                <b>Klik Peta Untuk Mendapatkan Koordinat</b>
            </td>
        </tr>

        <tr>
        	<td colspan="2" style="text-align: center;">
                <div class="panel">
                    <div class="panel-body">
                    <div id="mapid"></div>
                    </div>
                </div>
            </td>
        </tr>
        
        <tr>
        	<td>
                <b>Latitude</b>&nbsp;<font color="red">*</font>
            </td>
        	<td>
                <div class="col-sm-4">
                    <input type="text" autocomplete="off" placeholder="Latitude" class="form-control" name="latitude" id="latitude" />
                </div>
                <div class="col-sm-4">
                    <i>(dalam Decimal Degress). Cth : -7.797068</i>
                </div>
            </td>
        </tr>
        
        <tr>
        	<td>
                <b>Longitude</b>&nbsp;<font color="red">*</font>
            </td>
        	<td>
                <div class="col-sm-4">
                    <input type="text" autocomplete="off" placeholder="Longitude" class="form-control" name="longitude" id="longitude" />
                </div>
                <div class="col-sm-4">
                    <i>(dalam Decimal Degress). Cth : 110.370527</i>
                </div>
            </td>
        </tr>
    </tbody>
</table>

<div class="panel">
   	<div class="panel-heading">
		<span class="panel-title"><b>List Koordinat</b></span>
		<div class="panel-heading-controls">
			<a href="" class="btn btn-flat btn-sm btn-labeled btn-success xhr dest_subcontent-element"><span class="btn-label-icon left fa fa-plus"></span> Tambah</a>
		</div>
	</div>

    <div class="panel-body">
    	<div class="table-primary" style="overflow: auto;">
    		<table class="table table-bordered table-hover table-striped">
    			<thead>
    				<tr>
    					<th style="text-align: center; vertical-align: center">No.</th>
                        <th style="text-align: center; vertical-align: center">Group</th>
    					<th style="text-align: center; vertical-align: center">Latitude</th>
    					<th style="text-align: center; vertical-align: center">Longitude</th>
                        <th style="text-align: center; vertical-align: center">Aksi</th>
    				</tr>
    			</thead>
                
    			<tbody>
    			<?php
                    if (!empty($detailTransaksi)) {
    					$i =0;
    					foreach ($detailTransaksi as $row) {
    					$i++;
    				?>
    					<tr>
    						<td style="text-align: center;"><?php //echo $i; ?></td>
                            <td style="text-align: center;"><?php //echo $row->kodeSubKel; ?></td>
                            <td style="text-align: center;"><?php //echo $row->brgNama; ?></td>
                            <td style="text-align: center;"><?php //echo $row->nup; ?></td>
                            <td style="text-align: center;"><?php //echo $row->tglBuku; ?></td>
                        </tr>
    			<?php 
                        }
    			} else {
    				echo "<tr><td colspan='5'><div class='alert alert-danger'>Data Tidak Ditemukan</div></td></tr>";
    			}
    			?>
                </tbody>
            </table>
        </div>
        
        <div class="row text-center">
            <div class="col-sm-7">
        		<div class="panel-footer">
                    <button class="btn btn-flat btn-primary" type="submit" name="tombol_proses" value="simpan"><span class="btn-label-icon left fa fa-save"></span><b>Simpan</b></button>
                    <button class="btn btn-flat btn-default" id=""><span class="btn-label-icon left fa fa-undo"></span><b>Batal</b></button>
        		</div>
            </div>
        </div>
    </div>
</div>