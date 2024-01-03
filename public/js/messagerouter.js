$(function () {
  $('[data-toggle="tooltip"]').tooltip();

  $('#logSelector').on('change', function () {
    let selectedLog = $(this).val();

    document.location = '/logsviewer/' + selectedLog;
  });

  $('#cachebuster-action').on('change', function () {
    $('#cachebuster-response-display').addClass('d-none');
  });

  $('#processCacheBusterButton').on('click', function () {
    let formVars = $('#cachebuster-form').serialize();

    $('#cachebuster-response-display').addClass('d-none');

    $.ajax({
      url: '/testingtools/bustcache',
      type: 'POST',
      data: formVars,
      cache: false,
      success: function (data) {
        if (data.success) {
          $('#cachebuster-response-display')
            .html(data.success)
            .removeClass('d-none');
        }
      },
    });
  });

  $('#relaymessageButton').on('click', function () {
    let formVars = $('#relaymessage-form').serialize();

    $('#relaymessage-response-display').addClass('d-none');

    $.ajax({
      url: '/testingtools/sendmessage',
      type: 'POST',
      data: formVars,
      cache: false,
      success: function (data) {
        if (data.success) {
          $('#relaymessage-response-display')
            .html(data.success)
            .removeClass('d-none');
        }
      },
    });
  });

  $('#fakeweberrorButton').on('click', function () {
    let formVars = $('#fakeweberror-form').serialize();

    $('#fakeweberror-response-display').addClass('d-none');

    $.ajax({
      url: '/testingtools/fakeweberorr',
      type: 'POST',
      data: formVars,
      cache: false,
      success: function (data) {
        if (data.success) {
          $('#fakeweberror-response-display')
            .html(data.success)
            .removeClass('d-none');
        }
      },
    });
  });
});
