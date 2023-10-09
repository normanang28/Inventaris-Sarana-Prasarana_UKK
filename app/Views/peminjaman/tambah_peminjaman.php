<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="card">
        <div class="card-body">
            <div class="basic-form">
                <form class="form-horizontal form-label-left" novalidate  action="<?= base_url('home/aksi_tambah_peminjaman')?>" method="post">
                    <div class="row">
                     <div class="col-md-6 mb-3">
                        <label class="form-label">Inventaris<span style="color: red;">*</span></label>
                        <select name="id_inventaris" class="form-control text-capitalize" id="id_inventaris" required>
                            <option>Pilih Inventaris</option>
                            <?php 
                            foreach ($j as $jabatan) {
                                if (session()->get('level') == 1 || session()->get('level') == 2 || session()->get('level') == 3) {
                                    if ($jabatan->keterangan_inventaris == "Dapat Digunakan") {
                                        ?>
                                        <option class="text-capitalize" value="<?php echo $jabatan->id_inventaris ?>"><?php echo $jabatan->nama ?> / <?php echo $jabatan->kode_inventaris ?></option>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jumlah<span style="color: red;">*</span></label>
                        <input type="number" id="jumlah_pinjam" name="jumlah_pinjam" class="form-control text-capitalize" placeholder="Jumlah">
                    </div>
                    <div class="input input-group">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12">Tanggal Pengembalian<span style="color: red;">*</span></label>
                        <div class="col-12"></div>
                        <input type="datetime-local" id="tanggal_pengembalian" name="tanggal_pengembalian" placeholder="Tanggal Pengembalian" required="required" class="form-control">
                    </div> 
                </div>
                <div class="mt-3">
                    <a href="<?= base_url('/home/peminjaman')?>" type="submit" class="btn btn-primary">Cancel</a>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
