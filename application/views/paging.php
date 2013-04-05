<!--<script type="text/javascript">
var panjang = $('#table').attr('width');
$('#controls').attr('style', 'width: '+panjang);
</script>
<div id="controls">
    <div id="perpage">
            <select onchange="sorter.size(this.value)">
            <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15" selected="selected">15</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
            </select>
            <span>Entries Per Page</span>
    </div>
    <div id="navigation">
        <img src="<?= base_url('assets/js/sorter/images/first.gif') ?>" width="16" height="16" alt="First Page" onclick="sorter.move(-1,true)" />
            <img src="<?= base_url('assets/js/sorter/images/previous.gif') ?>" width="16" height="16" alt="First Page" onclick="sorter.move(-1)" />
            <img src="<?= base_url('assets/js/sorter/images/next.gif') ?>" width="16" height="16" alt="First Page" onclick="sorter.move(1)" />
            <img src="<?= base_url('assets/js/sorter/images/last.gif') ?>" width="16" height="16" alt="Last Page" onclick="sorter.move(1,true)" />
    </div>
    <div id="text">Displaying Page <span id="currentpage"></span> of <span id="pagelimit"></span></div>
</div>
    <script type="text/javascript" src="<?= base_url('assets/js/sorter/script.js') ?>"></script>
    <script type="text/javascript">
var sorter = new TINY.table.sorter("sorter");
    sorter.head = "head";
    sorter.asc = "asc";
    sorter.desc = "desc";
    sorter.even = "evenrow";
    sorter.odd = "oddrow";
    sorter.evensel = "evenselected";
    sorter.oddsel = "oddselected";
    sorter.paginate = true;
    sorter.currentid = "currentpage";
    sorter.limitid = "pagelimit";
    sorter.init("table",1);
  </script>-->