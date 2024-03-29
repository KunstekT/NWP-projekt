$('textarea.mention').mentionsInput({
    onDataRequest:function (mode, query, callback) {
      $.getJSON('/api/get-users', function(responseData) {
        responseData = _.filter(responseData, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });
        callback.call(this, responseData);
      });
    }
});