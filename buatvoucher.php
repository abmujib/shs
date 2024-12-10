<?php
date_default_timezone_set('Asia/Jakarta');
$date = date('dHis');
if(isset($_POST['kirim'])){
    $profile = $_POST['profile'];
    $saldo = $_POST['saldo'];
    $mac = $_POST['mac'];
}
?>
 
</center> 
<form action="https://hotspot.steksa.co.id/bayar.php" method="POST">  
</div>
&nbsp;KODE VOUCHER ANDA&nbsp;
<?php echo $date; ?></p>
<input class="form-control" type="text" placeholder="Nama Anda" required="" name="nama">
<input class="form-control" type="text" placeholder="Email Anda" required="" name="email">
<input class="form-control" type="number" placeholder="No Whatsapp" required="" name="phone">
<input type="hidden" value="<?php echo $date; ?>" name="vc"></input>
<input type="hidden" value="<?php echo $profile; ?>" name="profile"></input>
<input type="hidden" value="<?php echo $saldo; ?>" name="saldo"></input>
<input type="hidden" value="<?php echo $mac; ?>" name="mac"></input>
<p style="color:red; text-align: center;">Kode voucher berhasil dibuat, silahkan melakukan pembayaran agar voucHer anda bisa digunakan.</p>
<select name="rek" style="width: 100%;">
<option value="QRIS" selected>QRIS by ShopeePay</option>
<option value="DANA">DANA</option>
</select>
</br>
<center><button type="submit" name="bayar">Lanjutkan Pembayaran</button>
</form>