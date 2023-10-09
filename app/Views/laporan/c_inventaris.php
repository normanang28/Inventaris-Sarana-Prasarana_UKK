<table id="datatable-buttons" align="center" border="1" width="80%" class="table table-striped table-bordered">
  <thead>
    <tr>
      <th class="text-center">No</th>
      <th class="text-center">Nama Inventaris</th>
      <th class="text-center">Kode Inventaris</th>
      <th class="text-center">Kondisi</th>
      <th class="text-center">Keterangan</th>
      <th class="text-center">Jumlah</th>
      <th class="text-center">Nama Jenis</th>
      <th class="text-center">Nama Ruang</th>
      <th class="text-center">Tanggal</th>
  </tr>
</thead>
<tbody>
    <?php
    $no = 1;
    foreach ($jofinson as $jo) {
      if ($jo->keterangan_inventaris == "Dapat Digunakan") {
        ?>
        <tr>
          <th class="text-capitalize text-center"><?php echo $no++ ?></th>
          <td class="text-capitalize text-center"><?php echo $jo->nama?></td>
          <td class="text-capitalize text-center"><?php echo $jo->kode_inventaris?></td>
          <td class="text-capitalize text-center"><?php echo $jo->kondisi?></td>
          <td class="text-capitalize text-center"><?php echo $jo->keterangan_inventaris?></td>
          <td class="text-capitalize text-center"><?php echo $jo->jumlah?></td>
          <td class="text-capitalize text-center"><?php echo $jo->nama_jenis?></td>
          <td class="text-capitalize text-center"><?php echo $jo->nama_ruang?></td>
          <td class="text-capitalize text-center"><?php echo $jo->tanggal_laporan_inventaris?></td>
      </tr>
      <?php
  }
}
?>
</tbody>
</table>
</div>
<script>
  window.print();
</script>
