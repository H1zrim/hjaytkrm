</main>
</div></div><div class="modal-overlay" id="logoutModal">
  <div class="modal-box" style="max-width:380px;">
    <div class="modal-header">
      <h3>🚪 Konfirmasi Logout</h3>
      <button class="modal-close" onclick="document.getElementById('logoutModal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <p style="font-size:14px;color:var(--text-mid);font-family:'Lora',serif;line-height:1.6;">
        Apakah Anda yakin ingin keluar dari panel admin?
      </p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('logoutModal').classList.remove('open')">Tidak, Kembali</button>
      <a href="<?= BASEURL; ?>admin/login/logout" class="btn btn-danger">Ya, Keluar</a>
    </div>
  </div>
</div>

<div class="modal-overlay" id="deleteModal">
  <div class="modal-box" style="max-width:380px;">
    <div class="modal-header">
      <h3>⚠️ Konfirmasi Hapus</h3>
      <button class="modal-close" onclick="document.getElementById('deleteModal').classList.remove('open')">✕</button>
    </div>
    <div class="modal-body">
      <p id="deleteModalMsg" style="font-size:14px;color:var(--text-mid);font-family:'Lora',serif;line-height:1.6;">
        Apakah Anda yakin ingin menghapus data ini? Tindakan tidak dapat dibatalkan.
      </p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('deleteModal').classList.remove('open')">Tidak</button>
      <a href="#" id="deleteConfirmBtn" class="btn btn-danger">Ya, Hapus</a>
    </div>
  </div>
</div>

<script>
// Sidebar toggle
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('sidebarOverlay').classList.toggle('open');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebarOverlay').classList.remove('open');
}

// Delete confirmation
function confirmDelete(url, msg) {
  document.getElementById('deleteModalMsg').textContent = msg || 'Apakah Anda yakin ingin menghapus data ini?';
  document.getElementById('deleteConfirmBtn').href = url;
  document.getElementById('deleteModal').classList.add('open');
}

// Auto-close alert after 4s
setTimeout(() => {
  document.querySelectorAll('.alert').forEach(a => {
    a.style.transition = 'opacity .4s';
    a.style.opacity = '0';
    setTimeout(() => a.remove(), 400);
  });
}, 4000);

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
</script>
</body>
</html>