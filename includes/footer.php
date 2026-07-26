<?php
/**
 * SGRC - Main Footer Template
 * القالب الرئيسي للفوتر
 * 
 * Usage: require_once __DIR__ . '/../../includes/footer.php';
 */
?>
            </div><!-- /content-wrapper -->

            <!-- Footer -->
            <footer class="main-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <span>
                        <i class="bi bi-building"></i>
                        <?php echo app()->setting('commune_name', trans('app_name')); ?>
                    </span>
                    <span>
                        SGRC v1.0 &copy; <?php echo date('Y'); ?> 
                        | <?php echo trans('app_name'); ?>
                    </span>
                </div>
            </footer>

        </div><!-- /main-content -->
    </div><!-- /wrapper -->

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (for DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>

    <script>
        // Add loaded class for fade-in effect
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('loaded');
        });
    </script>
</body>
</html>