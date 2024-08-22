<!-- Limit to 3 Links each side of the current page -->
<?php $pager->setSurroundCount(3)  ?>
<!-- END-->

<nav aria-label="...">
    <ul class="pagination pagination-sm mb-0">
        <!-- Previous and First Links if available -->
        <?php if ($pager->hasPrevious()) { ?>
            <li class="page-item">
                <a href="<?= $pager->getFirst() ?>" class="page-link">First</a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getPrevious() ?>" class="page-link">Previous</a>
            </li>
        <?php }  ?>
        <!-- End of Previous and First -->

        <!-- Page Links -->

        <?php foreach ($pager->links() as $link) { ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>"><a class="page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a></li>
        <?php } ?>
        <!-- End of Page Links -->

        <!-- Next and Last Page -->
        <?php if ($pager->hasNext()) { ?>
            <li class="page-item">
                <a href="<?= $pager->getNext() ?>" class="page-link">Next</a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getLast() ?>" class="page-link">Last</a>
            </li>
        <?php } ?>
        <!-- End of Next and Last Page -->
    </ul>
</nav>