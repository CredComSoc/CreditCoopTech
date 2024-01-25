/**
 * Example js for autocompleting the path/account names.
 * Depends on jquery.
 * 
 * Some items need replacing.
 * 'http://ccleaf' is the url of the trunkward node.
 * 'my_user_name', 'my_auth_key' are the credentials for the trunkward node.
 * 
 * @todo credentials shouldn't be stored in javascript. Help needed!
 */

/**
 * the css id of the textfield.
 * @type String
 */
var path_field_id = '#accountnames';

/**
 * the url of the trunkward node.
 * @type String
 */
var trunkward_url = 'http://trunk.creditcommons.net';

/**
 * The login name for the trunkward node
 * @type String
 */
var ccuser = 'my_user_name';

/**
 * The auth key for the trunkward node.
 * @type String
 */
var ccauth = 'my_auth_key';

$( function() {
  // auto-complete using jquery
  $( path_field_id ).autocomplete({
    source: function( request, response ) {
      $.ajax( {
        url: trunkward_url + "/account/names/" + request.term,
        dataType: "json",
        contentType: "application/json;  charset=utf-8",
        headers: {
          'cc-user': ccuser,
          'cc-auth': ccauth
        },
       //data: {fragment: request.term},
        success: function( data ) {
          response( data );
        }
      } );
    },
    minLength: 1,
  } );
} );
