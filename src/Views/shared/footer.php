</main>

<footer class="bg-(--color-primary) text-white font-bold py-8 px-4 shadow-lg border-t border-(--color-primary)/20">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <img class="h-10 w-auto opacity-90 hover:opacity-100 transition-opacity" src="/img/lost_found.png" alt="Logo">
                <p class="text-sm md:text-base">Lost Nexus &copy; <?php echo date('Y'); ?></p>
            </div>
        </div>
    </div>

</footer>

<?php use App\Helpers\AssetsHelper; ?>
<script src="<?php echo AssetsHelper::js('chart.js'); ?>"></script>
<script src="<?php echo AssetsHelper::js('charts.min.js'); ?>" defer></script>
<script src="<?php echo AssetsHelper::js('main.js'); ?>" defer></script>
</body>

</html>