<?php
session_start();

if (isset($_SESSION['user_id'])) {
     // User is authenticated, allow access to the protected page
 } else {
     // Redirect to the login page or display an error message
     header("Location: ../index.php"); // Redirect to your login page
     exit();

 }
 
?>

<!DOCTYPE html>
<html lang="en">

<head>

     <title>Blog Website</title>

     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=Edge">
     <meta name="description" content="">
     <meta name="keywords" content="">
     <meta name="author" content="">
     <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

     <link rel="stylesheet" href="css/bootstrap.min.css">
     <link rel="stylesheet" href="css/font-awesome.min.css">
     <link rel="stylesheet" href="css/owl.carousel.css">
     <link rel="stylesheet" href="css/owl.theme.default.min.css">

     <!-- MAIN CSS -->
     <link rel="stylesheet" href="css/style.css">

</head>

<body id="top" data-spy="scroll" data-target=".navbar-collapse" data-offset="50">
     <main>
          <!-- MENU -->
          <section class="navbar custom-navbar navbar-fixed-top" role="navigation">
               <div class="container">

                    <div class="navbar-header">
                         <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                              <span class="icon icon-bar"></span>
                              <span class="icon icon-bar"></span>
                              <span class="icon icon-bar"></span>
                         </button>

                         <!-- lOGO TEXT HERE -->
                         <a href="#" class="navbar-brand">Blog Website</a>
                    </div>

                    <!-- MENU LINKS -->
                    <div class="collapse navbar-collapse">
                         <ul class="nav navbar-nav navbar-nav-first">
                              <li><a href="index.php">Home</a></li>
                              <li><a href="blog-posts.php">Blog</a></li>
                         </ul>
                    </div>

               </div>
          </section>

          <section>
               <div class="container" id="container">
                    <?php
                    $postId = $_GET['id'];
                    ?>

                    <script>
                         // Make a GET request to the PHP file
                         fetch(`method.php?id=<?php echo $postId; ?>`)
                              .then(response => response.json())
                              .then(data => {

                                   const row = document.getElementById('container');

                                   // Check if there's at least one item in the data array
                                   data.forEach(item => {
                                   

                                        row.innerHTML = `
                                        <h2>${item.title}</h2>
                                        <p class="lead">
                                        <i class="fa fa-user"></i> ${item.author} &nbsp;&nbsp;&nbsp;
                                        <i class="fa fa-calendar"></i> ${item.publishedDate} &nbsp;&nbsp;&nbsp;
                                        </p>
                                        <img src="images/other-image-fullscreen-1-1920x700.jpg" class="img-responsive" alt="">
                                        <br>
                                        <p>${item.content}</p>
                                        <br>
                                        <br>
                                   `;

                                   });
                              })
                              .catch (error => console.error(error));
                    </script>
               </div>
               <div class="container">
                    <h4>Comments</h4>

                    <div class="row" id="rowComment">
                         <script>
                              // Make a GET request to the PHP file
                              fetch(`method.php?id=<?php echo $postId; ?>`)
                                   .then(response => response.json())
                                   .then(data => {
                                        const row = document.getElementById('rowComment');

                                        // Check if there are no comments
                                        if (data.length < 1) {
                                             row.innerHTML = "No comments available.";
                                        } else {
                                             // Clear existing content before adding new comments
                                             row.innerHTML = "";

                                             // Process the data and insert it into the provided HTML structure
                                             data.forEach(item => {
                                                  const commentDiv = document.createElement("div");
                                                  commentDiv.setAttribute('data-comment-id', item.id); // Add data-comment-id attribute

                                                  commentDiv.innerHTML = `
                                                       <p class="comment-name"><b><u>${item.name}</u></b></p>
                                                       <p class="comment-email"><i>${item.email}</i></p>
                                                       <p class="comment-message" style="font-size: 1em">${item.message}</p>
                                                       <button type="button" onclick="deleteComment(${item.id})">Delete</button>
                                                       <button type="button" onclick="editComment(${item.id})">Editar Comentario</button>
                                                       <hr>
                                                  `;

                                                  row.appendChild(commentDiv);
                                             });
                                        }
                                   })
                                   .catch(error => console.error(error));


                         </script>

                         <script>
                              //                      EDIT BUTTON FUNCTION
                              let currentCommentId = null;
                              let tempEditedFields = {};
                              let actualEditedFields = {};
                               // Object to store edited fields
                              function editComment(comment_id) {


                                   //document.getElementById("editCommentModal").style.display = "block";
                                   currentCommentId = comment_id;
                                   const comment = document.querySelector(`[data-comment-id="${comment_id}"]`);
                                   const name = comment.querySelector(".comment-name").innerText;
                                   const email = comment.querySelector(".comment-email").innerText;
                                   const message = comment.querySelector(".comment-message").innerText;

                                   document.getElementById("editName").value = name;
                                   document.getElementById("editEmail").value = email;
                                   document.getElementById("editMessage").value = message;


                                    tempEditedFields['name'] = name;
                                    tempEditedFields['email'] = email;
                                    tempEditedFields['message'] = message;

                                   openModal(comment_id);
                              }

                              function openModal(comment_id) {
                                   document.getElementById("editCommentModal").style.display = "block";
                              }

                              function closeModal() {
                                   document.getElementById("editCommentModal").style.display = "none";
                              }

                              function saveCommentEdit() {
                                   const name = document.getElementById("editName").value;
                                   const email = document.getElementById("editEmail").value;
                                   const message = document.getElementById("editMessage").value;
                              
                                   
                                   // Check which fields are edited and store them in the tempEditedFields object
                                   if (name !== tempEditedFields.name) {
                                        actualEditedFields.name = name;
                                   }
                                   if (email !== tempEditedFields.email) {
                                        actualEditedFields.email = email;
                                   }
                                   if (message !== tempEditedFields.message) {
                                        actualEditedFields.message = message;
                                   }

                                   // Send a PATCH request if there are edited fields
                                   if (Object.keys(actualEditedFields).length < 3) {
                                        fetch('method.php', {
                                             method: 'PATCH', // Use PATCH method
                                             headers: {
                                                  'Content-Type': 'application/json',
                                             },
                                             body: JSON.stringify({
      comment_id: currentCommentId,
      editedFields: actualEditedFields, // Send the edited fields
    }),
                                        })
                                             .then(response => response.text())
                                             .then(data => {
                                                  console.log(data); // Handle the response as needed

                                                  // Update the UI here if necessary (e.g., update the displayed comment with the edited data)
                                                  // ...

                                                  closeModal();
                                             })
                                             .catch(error => console.error('Error:', error));
                                   } else {
                                        // Send the edited data to the server using a PUT request
                                        fetch('method.php', {
                                             method: 'PUT',
                                             headers: {
                                                  'Content-Type': 'application/json',
                                             },
                                             body: JSON.stringify({
                                                  comment_id: currentCommentId,
                                                  name: name,
                                                  email: email,
                                                  message: message,
                                             }),
                                        })
                                             .then(response => response.text())
                                             .then(data => {
                                                  // Handle the response as needed
                                                  console.log(data); // For example, log the response to the console

                                                  // Update the UI here if necessary (e.g., update the displayed comment with the edited data)
                                                  // ...

                                                  closeModal();
                                             })
                                             .catch(error => console.error('Error:', error));


                                   }



                              }
                         </script>

                         <script>
                              //              DELETE FUNCTION BUTTON
                              function deleteComment(comment_id) {
                                   const confirmation = window.confirm("Are you sure you want to delete this comment?");
                                   if (confirmation) {
                                        // User confirmed the deletion
                                        fetch('method.php', {
                                             method: 'DELETE',
                                             headers: {
                                                  'Content-Type': 'application/json',
                                             },
                                             body: JSON.stringify({ comment_id }),
                                        })
                                             .then(response => response.text())
                                             .then(data => {
                                                  // You can handle the response as needed
                                                  console.log(data); // For example, log the response to the console
                                                  // Consider updating the UI without a full page reload
                                                  // You can remove the comment from the DOM here
                                             })
                                             .catch(error => console.error('Error:', error));
                                        //location.reload();
                                   } else {
                                        // User canceled the deletion
                                        // You can handle this case or provide feedback to the user
                                   }
                              }
                         </script>

                    </div>



                    <form id="commentForm">
                         <input type="text" name="name" placeholder="Nombre" required>
                         <input type="email" name="email" placeholder="Correo Electrónico" required>
                         <textarea name="message" placeholder="Comentario" required></textarea>
                         <input type="hidden" name="idBlog" value="<?php echo $postId; ?>">
                         <!-- Reemplaza "ID_DEL_BLOG" con el ID correcto del blog -->
                         <button type="submit">Enviar Comentario</button>
                    </form>
                    <div id="responseMessage"></div>

                    <script>
                         const formulario = document.getElementById('commentForm');
                         const resultadoDiv = document.getElementById('responseMessage');

                         formulario.addEventListener('submit', function (event) {
                              event.preventDefault();



                              const formData = new FormData(formulario);

                              fetch('method.php', {
                                   method: 'POST',
                                   body: formData
                              })
                                   .then(response => response.text())
                                   .then(data => {
                                        resultadoDiv.textContent = data;
                                        formulario.reset();
                                        //location.reload();
                                   })
                                   .catch(error => console.error('Error:', error));
                         });
                    </script>

                    <!--          EDIT COMMENT MODAL        -->
                    <div id="editCommentModal" class="modal">
                         <div class="modal-content">
                              <span class="close" onclick="closeModal()">&times;</span>
                              <h3>Edit Comment</h3>
                              <form id="editCommentForm">
                                   <input type="text" name="name" id="editName" placeholder="Nombre">
                                   <input type="email" name="email" id="editEmail" placeholder="Correo Electrónico">
                                   <textarea name="message" id="editMessage" placeholder="Comentario"></textarea>
                                   <button type="button" id="saveEditButton" onclick="saveCommentEdit()">Guardar
                                        Cambios</button>
                                   <button type="button" id="cancelEditButton" onclick="closeModal()">Cancelar
                                        Cambios</button>
                              </form>
                         </div>
                    </div>
               </div>
          </section>
     </main>

     <!-- FOOTER -->


     <footer id="footer">
          <div class="container">
               <div class="row">

                    <div class="col-md-4 col-sm-6">
                         <div class="footer-info">
                              <div class="section-title">
                                   <h2>Headquarter</h2>
                              </div>
                              <address>
                                   <p>212 Barrington Court <br>New York, ABC 10001</p>
                              </address>

                              <ul class="social-icon">
                                   <li><a href="#" class="fa fa-facebook-square" attr="facebook icon"></a></li>
                                   <li><a href="#" class="fa fa-twitter"></a></li>
                                   <li><a href="#" class="fa fa-instagram"></a></li>
                              </ul>

                              <div class="copyright-text">
                                   <p>Copyright &copy; 2020 Company Name</p>
                                   <p>Template by: <a href="https://www.phpjabbers.com/">PHPJabbers.com</a></p>
                              </div>
                         </div>
                    </div>

                    <div class="col-md-4 col-sm-6">
                         <div class="footer-info">
                              <div class="section-title">
                                   <h2>Contact Info</h2>
                              </div>
                              <address>
                                   <p>+1 333 4040 5566</p>
                                   <p><a href="mailto:contact@company.com">contact@company.com</a></p>
                              </address>

                              <div class="footer_menu">
                                   <h2>Quick Links</h2>
                                   <ul>
                                        <li><a href="index.html">Home</a></li>
                                        <li><a href="about-us.html">About Us</a></li>
                                        <li><a href="terms.html">Terms & Conditions</a></li>
                                        <li><a href="contact.html">Contact Us</a></li>
                                   </ul>
                              </div>
                         </div>
                    </div>

                    <div class="col-md-4 col-sm-12">
                         <div class="footer-info newsletter-form">
                              <div class="section-title">
                                   <h2>Newsletter Signup</h2>
                              </div>
                              <div>
                                   <div class="form-group">
                                        <form action="#" method="get">
                                             <input type="email" class="form-control" placeholder="Enter your email"
                                                  name="email" id="email" required>
                                             <input type="submit" class="form-control" name="submit" id="form-submit"
                                                  value="Send me">
                                        </form>
                                        <span><sup>*</sup> Please note - we do not spam your email.</span>
                                   </div>
                              </div>
                         </div>
                    </div>

               </div>
          </div>
     </footer>


     <!-- SCRIPTS -->
     <script src="js/jquery.js"></script>
     <script src="js/bootstrap.min.js"></script>
     <script src="js/owl.carousel.min.js"></script>
     <script src="js/smoothscroll.js"></script>
     <script src="js/custom.js"></script>


</body>

</html>