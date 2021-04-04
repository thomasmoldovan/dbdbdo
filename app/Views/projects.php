<? echo view("projects/menu"); ?>
<div class="h-100 p-5">
    <div class="row h-100">
        <? $i = 0; while ($i <= 5) {
            $i++; ?>
            <div class="col-2 m-3 p-3 bg-secondary">
                <div class="media-body bg-light">
                    <strong>Project name here in bold</strong>
                    <br>
                    <small class="text-muted">Maybe some small description over here but not that long</small>
                    <div class="row d-flex">
                        <div class="col-7">
                            <div class="small"><i class="fa fa-pen mr-2"></i>5 Tables</div>
                            <div class="small"><i class="fa fa-pen mr-2"></i>9 Pages</div>
                            <small class="float-right text-navy"><b>Last updated: </b>3h ago</small>
                        </div>
                        <div class="col-5">
                            <div class="row no-gutters mt-1 float-right">
                                <div class="">
                                    <img src="https://picsum.photos/150/150" class="img-fluid pr-2" alt="Unsplash">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <? } ?>
    </div>
</div>
