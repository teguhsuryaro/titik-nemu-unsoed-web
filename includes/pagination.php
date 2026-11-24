<?php
/**
 * includes/pagination.php — Komponen Navigasi Halaman (Pagination) Bersama
 * Variabel opsional sebelum include:
 *   $page (int), $totalPages (int), $paginationBaseUrl (string),
 *   $paginationParams (array), $paginationAriaLabel (string), $paginationId (string)
 */
if (isset($totalPages) && $totalPages > 1):
  $pgBaseUrl   = $paginationBaseUrl ?? basename($_SERVER['PHP_SELF']);
  $pgParams    = $paginationParams ?? $_GET;
  $pgAriaLabel = $paginationAriaLabel ?? 'Navigasi halaman';
  $pgIdAttr    = !empty($paginationId) ? ' id="' . htmlspecialchars($paginationId) . '"' : '';
?>
  <nav class="pagination" aria-label="<?= htmlspecialchars($pgAriaLabel) ?>"<?= $pgIdAttr ?>>
    <?php if ($page > 1): ?>
      <?php $prevUrl = $pgBaseUrl . '?' . http_build_query(array_merge($pgParams, ['page' => $page - 1])); ?>
      <a href="<?= htmlspecialchars($prevUrl) ?>" class="pagination__link" aria-label="Halaman sebelumnya">
        <svg class="icon" aria-hidden="true"><use href="#i-arrow-left"/></svg>
      </a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
      <?php if ($p === 1 || $p === $totalPages || ($p >= $page - 1 && $p <= $page + 1)): ?>
        <?php $pageUrl = $pgBaseUrl . '?' . http_build_query(array_merge($pgParams, ['page' => $p])); ?>
        <a href="<?= htmlspecialchars($pageUrl) ?>"
           class="pagination__link <?= $p === $page ? 'pagination__link--active' : '' ?>"
           aria-current="<?= $p === $page ? 'page' : 'false' ?>">
          <?= $p ?>
        </a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
      <?php $nextUrl = $pgBaseUrl . '?' . http_build_query(array_merge($pgParams, ['page' => $page + 1])); ?>
      <a href="<?= htmlspecialchars($nextUrl) ?>" class="pagination__link" aria-label="Halaman selanjutnya">
        <svg class="icon" aria-hidden="true"><use href="#i-arrow-right"/></svg>
      </a>
    <?php endif; ?>
  </nav>
<?php endif; ?>
