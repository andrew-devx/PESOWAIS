<?php
// includes/view_helpers.php

if (!function_exists('renderStatCard')) {
    function renderStatCard($title, $value, $subtitle, $valueColor, $iconClass, $borderColor) {
        echo "<div class='bg-white border-2 $borderColor rounded-lg p-4 shadow-sm hover:shadow-md transition-all duration-300 group'>";
        echo '<div class="flex items-start justify-between">';
        echo '<div class="flex-1">';
        echo "<p class='$valueColor text-xs font-semibold mb-2 uppercase'>$title</p>";
        echo "<p class='text-lg sm:text-2xl font-bold $valueColor mb-1 max-w-[120px]'>$value</p>";
        echo "<p class='text-xs $valueColor opacity-60'>$subtitle</p>";
        echo '</div>';
        echo "<div class='w-8 h-8 flex items-center justify-center flex-shrink-0 -mt-2'><i class='$iconClass text-lg flex-shrink-0'></i></div>";
        echo '</div></div>';
    }
}
?>
