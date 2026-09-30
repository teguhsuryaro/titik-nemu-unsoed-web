/**
 * js/fakultas-prodi.js — AJAX Dropdown Fakultas → Program Studi
 */

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  var fakultasSelect = document.getElementById('fakultas_id');
  var prodiSelect    = document.getElementById('prodi_id');
  var prodiHint      = document.querySelector('.prodi-hint');

  if (!fakultasSelect || !prodiSelect) return;

  fakultasSelect.addEventListener('change', function () {
    var fakultasId = this.value;

    // Kosongkan prodi
    prodiSelect.innerHTML = '<option value="">Pilih Program Studi</option>';
    
    if (!fakultasId) {
      prodiSelect.disabled = true;
      if (prodiHint) prodiHint.style.display = 'block';
      return;
    }

    prodiSelect.disabled = false;
    if (prodiHint) prodiHint.style.display = 'none';
    prodiSelect.innerHTML = '<option value="">Memuat data...</option>';

    // Fetch data
    fetch('ajax/get_prodi.php?fakultas_id=' + encodeURIComponent(fakultasId))
      .then(function (response) {
        if (!response.ok) throw new Error('Gagal mengambil data');
        return response.json();
      })
      .then(function (data) {
        prodiSelect.innerHTML = '<option value="">Pilih Program Studi</option>';
        if (data.length === 0) {
          prodiSelect.innerHTML = '<option value="">Tidak ada program studi</option>';
          prodiSelect.disabled = true;
          return;
        }

        // Isi dropdown
        data.forEach(function (prodi) {
          var option = document.createElement('option');
          option.value = prodi.id;
          option.textContent = prodi.jenjang + ' - ' + prodi.nama_prodi;
          prodiSelect.appendChild(option);
        });
      })
      .catch(function (error) {
        console.error('Error fetching prodi:', error);
        prodiSelect.innerHTML = '<option value="">Gagal memuat data</option>';
        prodiSelect.disabled = true;
      });
  });

  // Jika fakultas belum dipilih saat load, nonaktifkan prodi
  if (!fakultasSelect.value) {
    prodiSelect.disabled = true;
    if (prodiHint) prodiHint.style.display = 'block';
  }
});
