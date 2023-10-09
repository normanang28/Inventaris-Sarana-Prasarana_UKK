<table id="datatable-buttons" align="center" border="1" width="80%" class="table table-striped table-bordered">
  <thead>
    <tr>
      <th class="text-center">No</th>
      <th class="text-center">Username</th>
      <th class="text-center">Nama Inventaris</th>
      <th class="text-center">Kode Inventaris</th>
      <th class="text-center">Jumlah</th>
      <th class="text-center">Tanggal</th>
      <th class="text-center">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $no = 1;
    foreach ($jofinson as $jo) {
      ?>
      <tr>
        <th class="text-capitalize text-center"><?php echo $no++ ?></th>
        <td class="text-capitalize text-center"><?php echo $jo->username?></td>
        <td class="text-capitalize text-center"><?php echo $jo->nama?></td>
        <td class="text-capitalize text-center"><?php echo $jo->kode_inventaris?></td>
        <td class="text-capitalize text-center"><?php echo $jo->jumlah_pengembalian?></td>
        <td class="text-capitalize text-center"><?php echo $jo->tanggal_laporan2?></td>
      </tr>
    <?php } ?>
  </tbody>
</table>
</div>
<script>
  window.print();
</script>
