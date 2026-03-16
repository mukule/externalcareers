<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($app_name ?? 'CRVWWDA RECRUITMENT PORTAL') ?></title>

    <!-- Datatables CSS -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />

    <!-- Main Styles -->
    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet" />
    <link href="<?= base_url('css/main.css') ?>" rel="stylesheet" />

    <!-- Icons -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <!-- Trumbowyg -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/ui/trumbowyg.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">

    <style>
        /* Ensure the sticky footer */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1 0 auto;
        }
        footer {
            flex-shrink: 0;
        }
    </style>
</head>
<body>

    <!-- Main navigation -->
    <?= $this->include('partials/main_nav') ?>

    <!-- Main content -->
    <main class="flex-fill">
        <?= $this->include('partials/dashboard_messages') ?>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <?= $this->include('partials/footer') ?>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>

    <!-- Trumbowyg JS -->
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/trumbowyg.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/langs/en.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('js/scripts.js') ?>"></script>

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('assets/demo/chart-area-demo.js') ?>"></script>
    <script src="<?= base_url('assets/demo/chart-bar-demo.js') ?>"></script>

    <!-- Datatables JS -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('js/datatables-simple-demo.js') ?>"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>

    <!-- Initialize Trumbowyg with 200-word limit -->
    <script>
    $(document).ready(function() {
        if ($('#description').length) {
            const maxWords = 200;
            const $textarea = $('#description');

            $textarea.trumbowyg({
                lang: 'en',
                btns: [
                    ['formatting'],
                    ['bold', 'italic', 'underline', 'strikethrough'],
                    ['link'],
                    ['unorderedList', 'orderedList'],
                    ['removeformat']
                ]
            });

            $('.trumbowyg-editor').css('min-height', '100px');

            const $wordCount = $('<div id="wordCount" class="form-text">0 / 200 words</div>');
            $textarea.closest('.mb-3').append($wordCount);

            function updateWordCount() {
                const text = $textarea.trumbowyg('html').replace(/<[^>]*>/g, ' ').trim();
                const words = text.length ? text.split(/\s+/) : [];
                const wordLength = words.length;

                if (wordLength > maxWords) {
                    const trimmed = words.slice(0, maxWords).join(' ');
                    $textarea.trumbowyg('html', trimmed);
                }

                $wordCount.text(`${Math.min(wordLength, maxWords)} / ${maxWords} words`);
            }

            $textarea.on('tbwchange keyup paste', updateWordCount);
            updateWordCount();
        }
    });
    </script>

    <!-- Year picker -->
    <script>
    $(document).ready(function(){
        $('.yearpicker').datepicker({
            format: "yyyy",
            minViewMode: 2,
            autoclose: true,
            endDate: new Date()
        });
    });
    </script>

</body>
</html>
