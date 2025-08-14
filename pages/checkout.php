<?php
include '../includes/auth.php';
include '../includes/header.php';
include '../config/db.php';

$user_id = $_SESSION['user_id'];

$provinces = mysqli_query($conn, "SELECT * FROM provinces ORDER BY prov_name ASC");
?>

<main class="container mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">📦 Checkout</h1>

  <form action="../process/checkout_process.php" method="POST" class="space-y-4" id="checkout-form">
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block font-medium">Provinsi:</label>
        <select name="province_id" id="province" required class="w-full border px-3 py-2 rounded">
          <option value="">-- Pilih Provinsi --</option>
          <?php while ($row = mysqli_fetch_assoc($provinces)) : ?>
            <option value="<?= $row['prov_id'] ?>" data-ongkir="<?= $row['shipping_cost'] ?>">
              <?= htmlspecialchars($row['prov_name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
        <input type="hidden" name="prov_name" id="prov_name">
      </div>

      <div>
        <label class="block font-medium">Kota/Kabupaten:</label>
        <select name="city_id" id="city" required class="w-full border px-3 py-2 rounded">
          <option value="">-- Pilih Kota --</option>
        </select>
        <input type="hidden" name="city_name" id="city_name">
      </div>

      <div>
        <label class="block font-medium">Kecamatan:</label>
        <select name="district_id" id="district" required class="w-full border px-3 py-2 rounded">
          <option value="">-- Pilih Kecamatan --</option>
        </select>
        <input type="hidden" name="dis_name" id="dis_name">
      </div>

      <div>
        <label class="block font-medium">Kelurahan/Desa:</label>
        <select name="subdistrict_id" id="subdistrict" required class="w-full border px-3 py-2 rounded">
          <option value="">-- Pilih Kelurahan --</option>
        </select>
        <input type="hidden" name="subdis_name" id="subdis_name">
      </div>

      <div>
        <label class="block font-medium">Kode Pos:</label>
        <input type="text" name="postalcode" id="postalcode" readonly class="w-full border px-3 py-2 rounded bg-gray-100">
      </div>

      <div>
        <label class="block font-medium">No Telepon:</label>
        <input type="text" name="no_telp" required class="w-full border px-3 py-2 rounded">
      </div>
    </div>

    <div>
      <label class="block font-medium mt-4">Alamat Lengkap:</label>
      <textarea name="alamat_detail" required class="w-full border px-3 py-2 rounded" placeholder="Contoh: Jl. Melati No. 123 RT 04 RW 03"></textarea>
    </div>

    <div class="mt-4 bg-gray-50 p-4 rounded border">
      <p><span class="font-semibold">Ongkir:</span> Rp <span id="ongkir">0</span></p>
      <p><span class="font-semibold">Pajak (11%):</span> Rp <span id="pajak">0</span></p>
      <p><span class="font-bold text-lg">Total Harga:</span> Rp <span id="harga_total">0</span></p>
    </div>

    <input type="hidden" name="ongkir_value" id="ongkir_value">
    <input type="hidden" name="pajak_value" id="pajak_value">
    <input type="hidden" name="harga_total_value" id="harga_total_value">

    <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
      ✅ Buat Pesanan
    </button>
  </form>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#province').on('change', function() {
    const id = $(this).val();
    const ongkir = $('option:selected', this).data('ongkir') || 0;
    $('#ongkir').text(Number(ongkir).toLocaleString());
    $('#ongkir_value').val(ongkir);
    updateTotal();

    const provName = $('option:selected', this).text();
    $('#prov_name').val(provName);

    $('#city').html('<option value="">Loading...</option>');
    $.get('../ajax/get_cities.php?prov_id=' + id, function(data) {
      $('#city').html(data);
      $('#district, #subdistrict').html('<option value="">-- Pilih --</option>');
      $('#postalcode').val('');
    });
  });

  $('#city').on('change', function() {
    const id = $(this).val();
    const name = $('option:selected', this).text();
    $('#city_name').val(name);

    $('#district').html('<option value="">Loading...</option>');
    $.get('../ajax/get_districts.php?city_id=' + id, function(data) {
      $('#district').html(data);
      $('#subdistrict').html('<option value="">-- Pilih --</option>');
      $('#postalcode').val('');
    });
  });

  $('#district').on('change', function() {
    const id = $(this).val();
    const name = $('option:selected', this).text();
    $('#dis_name').val(name);

    $('#subdistrict').html('<option value="">Loading...</option>');
    $.get('../ajax/get_subdistricts.php?dis_id=' + id, function(data) {
      $('#subdistrict').html(data);
      $('#postalcode').val('');
    });
  });

  $('#subdistrict').on('change', function() {
    const id = $(this).val();
    const name = $('option:selected', this).text();
    $('#subdis_name').val(name);

    $.get('../ajax/get_postalcode.php?subdis_id=' + id, function(data) {
      $('#postalcode').val(data);
    });
  });

  function updateTotal() {
    $.get('../ajax/get_total_checkout.php', function(res) {
      const total = parseInt(res);
      const ongkir = parseInt($('#ongkir_value').val()) || 0;
      const pajak = Math.ceil(total * 0.11);
      const harga_total = total + ongkir + pajak;

      $('#pajak').text(pajak.toLocaleString());
      $('#harga_total').text(harga_total.toLocaleString());
      $('#pajak_value').val(pajak);
      $('#harga_total_value').val(harga_total);
    });
  }

  $(document).ready(function() {
    updateTotal();
  });
</script>

<?php include '../includes/footer.php'; ?>