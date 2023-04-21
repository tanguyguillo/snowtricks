

var increment = 10;
var startFilter = 0;
var endFilter = increment;

var $this = $('.items');

var elementLength = $this.find('div.item').length;
$('.listLength').text(elementLength);

if (elementLength > 10) {
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
})

