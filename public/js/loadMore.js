// read more fc
// $(function () {
// items to show
var increment = 5;
var startFilter = 0;
var endFilter = increment;

// item selector
var $this = $('.items');

var elementLength = $this.find('div').length;
$('.listLength').text(elementLength);

if (elementLength > 5) {
    $('.buttonToogle').show();
}

$('.items .item').slice(startFilter, endFilter).addClass('shown');
$('.shownLength').text(endFilter);
$('.items .item').not('.shown').hide();
$('.buttonToogle .showMore').on('click', function () {
    if (elementLength > endFilter) {
        startFilter += increment;
        endFilter += increment;
        $('.items .item').slice(startFilter, endFilter).not('.shown').addClass('shown').toggle(500);
        $('.shownLength').text((endFilter > elementLength) ? elementLength : endFilter);
        if (elementLength <= endFilter) {
            $(this).remove();
        }
    }
});
