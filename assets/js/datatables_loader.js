import * as Turbo from "@hotwired/turbo";

document.addEventListener('turbo:load', initDataTables);

function initDataTables()
{
    //@ts-ignore
    $($.fn.DataTable.tables()).DataTable().fixedHeader.disable();
    //@ts-ignore
    $($.fn.DataTable.tables()).DataTable().destroy();

    //Find all datatables and init it.
    let $tables = $('[data-datatable]');
    $.each($tables, function(index, table) {
        let $table = $(table);
        let settings = $table.data('settings');

        //@ts-ignore
        var promise = $('#part_list').initDataTables(settings,
            {
                colReorder: true,
                responsive: true,
                "fixedHeader": { header: $(window).width() >= 768, //Only enable fixedHeaders on devices with big screen. Fixes scrolling issues on smartphones.
                    headerOffset: $("#navbar").height()},
                "buttons": [ {
                    "extend": 'colvis',
                    'className': 'mr-2 btn-light',
                    "text": "<i class='fa fa-cog'></i>"
                }],
                "select": $table.data('select') ?? false,
                "rowCallback": function( row, data, index ) {
                    //Check if we have a level, then change color of this row
                    if (data.level) {
                        let style = "";
                        switch(data.level) {
                            case "emergency":
                            case "alert":
                            case "critical":
                            case "error":
                                style = "table-danger";
                                break;
                            case "warning":
                                style = "table-warning";
                                break;
                            case "notice":
                                style = "table-info";
                                break;
                        }

                        if (style){
                            $(row).addClass(style);
                        }
                    }
                }
            });

        //Register links.
        promise.then(function() {
            //Set the correct title in the table.
            let title = $('#part-card-header-src');
            $('#part-card-header').html(title.html());
            triggerReloadEvent();


            if($table.data('part_table')) {
                //@ts-ignore
                $('#dt').on( 'select.dt deselect.dt', function ( e, dt, items ) {
                    let selected_elements = dt.rows({selected: true});
                    let count = selected_elements.count();

                    if(count > 0) {
                        $('#select_panel').removeClass('d-none');
                    } else {
                        $('#select_panel').addClass('d-none');
                    }

                    $('#select_count').text(count);

                    let selected_ids_string = selected_elements.data().map(function(value, index) {
                        return value['id']; }
                    ).join(",");

                    $('#select_ids').val(selected_ids_string);

                } );
            }

            //Attach event listener to update links after new page selection:
            $('#dt').on('draw.dt column-visibility.dt', function() {
                triggerReloadEvent();
            });
        });
    });

    console.debug('Datatables inited.');
}

function triggerReloadEvent()
{
    const event = new Event('dt:loaded');
    document.dispatchEvent(event);
}