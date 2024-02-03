<?php

/**
 * Show information with alert (JS)
 * 
 * @param string $message - Information message
 * @param string $redirect_to - Somewhere to redirect to
 */
function showInfo(
    string $message, 
    string $redirect_to=NULL
) {
    ?><script>
        alert("<?=htmlspecialchars($message)?>");
        <?php if (!is_null($redirect_to)): ?>
            window.location.href = "<?=$redirect_to?>"
        <?php endif; ?>
    </script><?php
}