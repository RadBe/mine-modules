<?php

if ($this->countPages < 2) {
    return '';
}

$pages = '';
$visiblePageStart = $this->currentPage <= 2 && $this->countPages >= 1 ? 1 : $this->currentPage - 1;
$visiblePageEnd = $this->currentPage >= $this->countPages - 1 && $this->countPages <= 1 || $this->currentPage == $this->countPages ? $this->countPages : $this->currentPage + 1;
if ($this->countPages > 1) {
    if ($visiblePageStart > 1 && $this->currentPage == $this->countPages) {
        $visiblePageStart--;
    }
    if ($visiblePageEnd < $this->countPages && $this->currentPage == 1) {
        $visiblePageEnd++;
    }
}

for ($i = $visiblePageStart; $i <= $visiblePageEnd; $i++)
{
    if ($i == $this->currentPage) {
        $pages .= '<span class="tw-pagination__item active">' . $i . '</span>';
    } else {
        $pages .= '<a href="' . $this->createUrl($i) . '" class="tw-pagination__item">' . $i . '</a>';
    }
}

if ($this->currentPage > 2 && $this->countPages >= 4) {
    $pages = '<a href="' . $this->createUrl(1) . '" class="tw-pagination__item">1</a><span class="tw-pagination__dots">...</span>' . $pages;
}

if ($this->currentPage + 2 <= $this->countPages && $this->countPages >= 4) {
    $pages .= '<span class="tw-pagination__dots">...</span>';
    $pages .= '<a href="' . $this->createUrl($this->countPages) . '" class="tw-pagination__item">' . $this->countPages . '</a>';
}

return "<div class='tw-pagination'>$pages</div>";
