<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="<?= esc($app_name ?? 'CRVWWDA RECRUITMENT PORTAL') ?>" />
    <meta name="author" content="<?= esc($app_name ?? 'CRVWWDA RECRUITMENT PORTAL') ?>" />
    <title><?= esc($app_name ?? 'CRVWWDA RECRUITMENT PORTAL') ?> | <?= esc($title ?? 'Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
    <link href="<?= base_url('css/styles.css') ?>" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/ui/trumbowyg.min.css">
    <link rel="icon" type="image/png" href="<?= base_url('favicon.ico') ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


    <style>

        .icon-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: rgba(76, 12, 171, 0.1);
}

.icon-circle i {
    font-size: 1.2rem;
    color: #4c0cab;
}


    </style>
</head>

<body class="sb-nav-fixed">

    
    <?= $this->include('partials/nav') ?>

    <div id="layoutSidenav">
       
        <?= $this->include('partials/sidebar') ?>

        <div id="layoutSidenav_content">
         <main>
            <?= $this->include('partials/dashboard_messages') ?>
            <?= $this->renderSection('content') ?>
        </main>


           
            <?= $this->include('partials/dashboard_footer') ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('js/scripts.js') ?>"></script>

   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    

   
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="<?= base_url('js/datatables-simple-demo.js') ?>"></script>
      <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/langs/en.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.28.0/dist/trumbowyg.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
      <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    
      
      <script>
$(document).ready(function() {
    const maxWords = 500;

    function initTrumbowyg(selector) {
        const $textarea = $(selector);

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

        // Set minimum height
        $('.trumbowyg-editor').css('min-height', '100px');

        // Create word count element and place directly after textarea
        const $wordCount = $('<div class="form-text text-end mt-1">0 / ' + maxWords + ' words</div>');
        $textarea.after($wordCount);

        function updateWordCount() {
            // Get text content without HTML
            const text = $textarea.trumbowyg('html').replace(/<[^>]*>/g, ' ').trim();
            const words = text.length ? text.split(/\s+/) : [];
            const wordLength = words.length;

            // Trim to max words
            if (wordLength > maxWords) {
                const trimmed = words.slice(0, maxWords).join(' ');
                $textarea.trumbowyg('html', trimmed);
            }

            $wordCount.text(`${Math.min(wordLength, maxWords)} / ${maxWords} words`);
        }

        $textarea.on('tbwchange keyup paste', updateWordCount);
        updateWordCount();
    }

    if ($('#summary').length) initTrumbowyg('#summary');
    if ($('#description').length) initTrumbowyg('#description');
});
</script>


</body>
</html>
