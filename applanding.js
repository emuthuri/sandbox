window.onload = function() {

          // Allow for vendor prefixes.
          window.requestFileSystem = window.requestFileSystem ||
                                     window.webkitRequestFileSystem;   


          // Create a variable that will store a reference to the FileSystem object.
          var filesystem = null;

          // Get references to the page elements.
          var form = document.getElementById('file-form');
          var filenameInput = document.getElementById('filename');
          var contentTextArea = document.getElementById('content');

          var fileList = document.getElementById('file-list');

          var messageBox = document.getElementById('messages');


          // A simple error handler to be used.
          //if an error occurs, it logs a message to the browser console
          function errorHandler(error) {
                var message = '';

                switch (error.code) {
                  case FileError.SECURITY_ERR:
                    message = 'Security Error';
                    break;
                  case FileError.NOT_FOUND_ERR:
                    message = 'Not Found Error';
                    break;
                  case FileError.QUOTA_EXCEEDED_ERR:
                    message = 'Quota Exceeded Error';
                    break;
                  case FileError.INVALID_MODIFICATION_ERR:
                    message = 'Invalid Modification Error';
                    break;
                  case FileError.INVALID_STATE_ERR:
                    message = 'Invalid State Error';
                    break;
                  default:
                    message = 'Unknown Error';
                    break;
                }

                console.log(message);
          }


          // Request a FileSystem and set the filesystem variable.
          //uses the requestFileSystem prefixed in the browser as webkitRequestFileSystem
         //The initFileSystem function requests that your app is given 5MB of persistent storage space
          function initFileSystem() {
            navigator.webkitPersistentStorage.requestQuota(1024 * 1024 * 5,
              function(grantedSize) {

                // Request a file system with the new size.
                window.requestFileSystem(window.PERSISTENT, grantedSize, function(fs) {

                  // Set the filesystem variable.
                  filesystem = fs;

                  // Setup event listeners on the form.
                  setupFormEventListener();

                  // Update the file browser.
                  listFiles();

                }, errorHandler);

              }, errorHandler);
          }

          
          function loadFile(filename) {
              //getFile() specifies the particular form
              //the getFile() retrieves the FileEntry for the file
                filesystem.root.getFile(filename, {}, function(fileEntry) {
                        //use the file method to get the file object
                        fileEntry.file(function(file) {
                                //the FileReaderstores this object in reader variable
                              var reader = new FileReader();

                              reader.onload = function(e) {
                                  // Update the form fields.
                                  filenameInput.value = filename;
                                  contentTextArea.value = this.result;
                              };

                              reader.readAsText(file);
                        }, errorHandler);
                }, errorHandler);
          }

          function displayEntries(entries) {
                // Clear out the current file browser entries.
                fileList.innerHTML = '';

                //loop through the entries array creating a <li> for each entry with two links
                entries.forEach(function(entry, i) {
                        var li = document.createElement('li');

                        //making if hyperlink
                        var link = document.createElement('a');
                        link.innerHTML = entry.name;
                        link.className = 'edit-file';
                        li.appendChild(link);

                        //making of delete button
                        var delLink = document.createElement('a');
                        delLink.innerHTML = '[x]';
                        delLink.className = 'delete-file';
                        li.appendChild(delLink);

                        fileList.appendChild(li);

                        // Setup an event listener that will load the file when the link
                        // is clicked.
                        link.addEventListener('click', function(e) {
                                e.preventDefault();
                                loadFile(entry.name);
                        });

                        // Setup an event listener that will delete the file when the delete link
                        // is clicked.
                        delLink.addEventListener('click', function(e) {
                                e.preventDefault();
                                deleteFile(entry.name);
                        });
                });
          }


          function listFiles() {
              //calling the createReader() in order to create a DirectoryReader on the root
                    var dirReader = filesystem.root.createReader();
                    //returned file entries in an array
                    var entries = [];

                    //fetchEntries is called over and over until all entries are displayed.
                    var fetchEntries = function() {
                            //fetching a block of entries
                            dirReader.readEntries(function(results) {
                                      if (!results.length) {
                                                //if no results are returned then we call the diplay to show entries
                                              displayEntries(entries.sort().reverse());
                                      } 
                                      else {
                                              entries = entries.concat(results);
                                              fetchEntries();
                                      }
                            }, errorHandler);
                    };

                    fetchEntries();
          }


          // Save a file in the FileSystem.
          function saveFile(filename, content) {
                    filesystem.root.getFile(filename, {create: true}, function(fileEntry) {
                            //the fileWriter method writes bytes to the gotten file
                            fileEntry.createWriter(function(fileWriter) {
                                    //create an event listener that responds after the write operation
                                      fileWriter.onwriteend = function(e) {
                                              // Update the file browser.
                                              listFiles();

                                              // Clean out the form field.
                                              filenameInput.value = '';
                                              contentTextArea.value = '';

                                              // Show a saved message.
                                              messageBox.innerHTML = 'File saved!';
                                      };
                                      
                                      //event listener in case an error occurs
                                      fileWriter.onerror = function(e) {
                                              console.log('Write error: ' + e.toString());
                                              alert('An error occurred and your file could not be saved!');
                                      };
                                      
                                      //blob is an object that contains raw data. it is written to the file
                                      var contentBlob = new Blob([content], {type: 'text/plain'});
                                      fileWriter.write(contentBlob);
                            }, errorHandler);
                    }, errorHandler);
          }


          function deleteFile(filename) {
                //retreive a fileEntry object using the getFile()
                    filesystem.root.getFile(filename, {create: false}, function(fileEntry) {
                            //call the remove() to delete the file
                            fileEntry.remove(function(e) {
                                      // Update the file browser.
                                      listFiles();

                                      // Show a deleted message.
                                      messageBox.innerHTML = 'File deleted!';
                            }, errorHandler);

                    }, errorHandler);
          }

          // Add event listeners on the form.
          function setupFormEventListener() {
                    form.addEventListener('submit', function(e) {
                            e.preventDefault();

                            // Get the form data.
                            var filename = filenameInput.value;
                            var content = contentTextArea.value;

                            // Save the file.
                            saveFile(filename, content);
                    });
          }

          // Start the app by requesting a FileSystem (if the browser supports the API)
          if (window.requestFileSystem) {
                    initFileSystem();
          } 
          else {
                    alert('Sorry! Your browser doesn\'t support the FileSystem API :(');
          }
};
