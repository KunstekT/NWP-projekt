$('textarea.mention').mentionsInput({
      onDataRequest:function (mode, query, callback) {
        $.getJSON('users.json', function(responseData) {
          responseData = _.filter(responseData, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });
          callback.call(this, responseData);
        });
      }

});

/*
$('textarea.mention').mentionsInput({
    onDataRequest:function (mode, query, callback) {
      var data = [
        { id:1, name:'Kenneth Auchenberg', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
        { id:2, name:'Kenneth Auchenberg2', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
        { id:3, name:'Anders Pollas', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
        { id:4, name:'Kasper Hulthin', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
        { id:5, name:'Andreas Haugstrup', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' },
        { id:6, name:'Pete Lacey', 'avatar':'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif', 'type':'contact' }
      ];
      
      data = _.filter(data, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) > -1 });
  
      callback.call(this, data);
    }
  });
  
  
  

  function userIsAFriend($userId) {
    // return in_array($userId, $friends);
    $friends = session('friends', []);
    foreach (session('friends', []) as $friend) {
        if ($friend->id === $userId) {
            return true;
        }
    }
    return false;
}  */