$('body').on('click', ".apple-menu-close", function(e){
    let me = $(e.target)
    let container = me.closest(".apple-menu-container")
    container.slideUp("slow")
})
$('body').on('click', ".apple", function(){
    let me = $(this)
    let menu = me.next(".apple-menu-container")
    menu.slideDown("slow")
})
$('body').on('click', ".eat", function(e){
    let me = $(e.target)
    let appleId = me.data('apple_id')
    if (me.data('status') == 2) {
        console.log(me.data('remain') > 0)
        if (me.data('remain') > 0) {
            $.ajax({
                method: "POST",
                url: '/apples/eat',
                dataType: 'html',
                data: {
                  "id": appleId
                },
                success: function(response) {
                    let container = me.closest(".apple-container")
                    container.after(response)
                    container.remove()
                }
            })
        } else {
            showAlert("Не ешьте огрызки!")
        }
    } else {
        let msg = ''
        if (me.data('status') == 3) {
            msg = "Яблоко сгнило. Поесть не получится..."
        } else {
            msg = "Яблоко на дереве. Надо ждать, пока упадет..."
        }
        showAlert(msg)
    }
})
function showAlert(msg) {
    $('#alert').text(msg)
    $('#alert').slideDown("slow")
    setTimeout(function(){
        $('#alert').slideUp()
    }, 3000)
}