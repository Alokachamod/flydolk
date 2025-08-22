function handleSearch() {
  // Find the input field relative to the button's parent
  const searchInput = document.querySelector(".search-input");
  const query = searchInput.value;

  // Only trigger the search if the input is focused and has a value
  if (document.activeElement === searchInput && query) {
    alert("Searching for: " + query);
    // You can replace the alert with your actual search logic,
    // like submitting a form or making an API call.
  } else if (document.activeElement !== searchInput) {
    // If the input is not focused, clicking the icon should focus it
    searchInput.focus();
  }
}

//sign up process

function signUp() {
  var n = document.getElementById("name");
  var e = document.getElementById("email");
  var p = document.getElementById("password");
  var m = document.getElementById("mobile");

  var form = new FormData();
  form.append("n", n.value);
  form.append("e", e.value);
  form.append("p", p.value);
  form.append("m", m.value);

  var request = new XMLHttpRequest();

  request.onreadystatechange = function () {
    if (request.readyState == 4) {
      var text = request.responseText;
      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "You have signed up successfully. You can now sign in.",
          confirmButtonText: "OK",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }
    }
  };

  request.open("POST", "signUpProcess.php", true);
  request.send(form);
}

//sign in process

function signin() {
  var e = document.getElementById("e").value;
  var p = document.getElementById("p").value;

  var form = new FormData();
  // FIX: Append the variables e and p directly, not e.value and p.value
  form.append("e", e);
  form.append("p", p);

  var request = new XMLHttpRequest();

  request.onreadystatechange = function () {
    // Check if the request is complete AND successful
    if (request.readyState == 4 && request.status == 200) {
      var text = request.responseText;

      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "You have signed in successfully.",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.href = "index.php"; // Redirect after successful sign-in
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }
    }
  };

  request.open("POST", "signInProcess.php", true);
  request.send(form);
}

//admin-login ani,ations and functions

document.addEventListener("DOMContentLoaded", function () {
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("adminPassword");

  /**
   * Toggles the password field visibility.
   */
  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
      // Toggle the type attribute of the password input
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);

      // Toggle the eye icon
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  }

  // The animations are handled by CSS, so no extra JavaScript is needed for them.
  // The form submission logic has been removed as requested.
});

function loginAdmin() {
  //alert("Admin login functionality is not implemented yet.");

  var ae = document.getElementById("adminEmail").value;
  var ap = document.getElementById("adminPassword").value;

  //alert("Admin email: " + ae + ", Admin password: " + ap);

  var form = new FormData();
  form.append("ae", ae);
  form.append("ap", ap);

  var request = new XMLHttpRequest();

  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var text = request.responseText;

      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "You have logged in successfully.",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.href = "admin-dashboard.php"; // Redirect after successful login
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }
    }
  };
  request.open("POST", "adminLoginProcess.php", true);
  request.send(form);
}

//add category process

function addCategory() {
  //alert("Add category functionality is not implemented yet.");

  var cname = document.getElementById("category").value;

  //alert("Category name: " + cname);

  var form = new FormData();
  form.append("c", cname);
  var request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var text = request.responseText;

      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "Category added successfully.",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.reload(); // Reload the page to see the new category
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }
    }
  };
  request.open("POST", "addCategory.php", true);
  request.send(form);
}

//delete category process

/**
 * Asks for confirmation and deletes a category using SweetAlert2.
 * @param {number} categoryId - The ID of the category to delete.
 * @param {string} categoryName - The name of the category for the confirmation dialog.
 */
function deleteCategory(categoryId, categoryName) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert the deletion of '" + categoryName + "'!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    // This code runs if the user clicks "Yes, delete it!"
    if (result.isConfirmed) {
      // Prepare the data to send (only the ID is needed)
      var form = new FormData();
      form.append("categoryId", categoryId);

      // Create and send the AJAX request
      var request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          var text = request.responseText;

          if (text.includes("success")) {
            // Show success message and reload
            Swal.fire({
              title: "Deleted!",
              text: "The category has been deleted.",
              icon: "success",
            }).then(() => {
              window.location.reload();
            });
          } else {
            // Show error message from the server
            Swal.fire({
              title: "Error!",
              text: text,
              icon: "error",
            });
          }
        }
      };
      request.open("POST", "deleteCategory.php", true);
      request.send(form);
    }
  });
}

//add brand process

function addBrand() {
  //alert("Add brand functionality is not implemented yet.");

  var bname = document.getElementById("bname").value;

  //alert("Brand name: " + bname);

  var Form = new FormData();
  Form.append("b", bname);
  var request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var text = request.responseText;

      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "Brand added successfully.",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.reload(); // Reload the page to see the new brand
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }
    }
  };
  request.open("POST", "addBrand.php", true);
  request.send(Form);
}

/**
 * Asks for confirmation and deletes a Brand using SweetAlert2.
 * @param {number} brandId - The ID of the brand to delete.
 * @param {string} brandName - The name of the brand for the confirmation dialog.
 */
function deleteBrand(brandId, brandName) {
  // <-- FIX 1: Renamed from deletebrand to deleteBrand
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert the deletion of '" + brandName + "'!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    // This code runs if the user clicks "Yes, delete it!"
    if (result.isConfirmed) {
      // Prepare the data to send (only the ID is needed)
      var form = new FormData();
      form.append("brandId", brandId);

      // Create and send the AJAX request
      var request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          var text = request.responseText;

          if (text.includes("success")) {
            // Show success message and reload
            Swal.fire({
              title: "Deleted!",
              text: "The brand has been deleted.",
              icon: "success",
            }).then(() => {
              window.location.reload();
            });
          } else {
            // Show error message from the server
            Swal.fire({
              title: "Error!",
              text: text,
              icon: "error",
            });
          }
        }
      };
      request.open("POST", "deleteBrand.php", true);
      request.send(form);
      // echo("success"); // <-- FIX 2: Removed invalid PHP code
    }
  });
}

function addColor() {
  var cname = document.getElementById("cname").value;

  //alert("Color name: " + cname + ", Color code: " + ccode);

  var form = new FormData();
  form.append("cname", cname);

  var request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var text = request.responseText;

      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "Color added successfully.",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.reload(); // Reload the page to see the new color
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }
    }
  };
  request.open("POST", "addColor.php", true);
  request.send(form);
}

/** * Asks for confirmation and deletes a color using SweetAlert2.
 * @param {string} colorId - The code of the color to delete.
 * @param {string} colorName - The name of the color for the confirmation dialog.
 */
function deleteColor(colorId, colorName) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert the deletion of '" + colorName + "'!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      var form = new FormData();
      form.append("colorId", colorId);

      var request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          var text = request.responseText;

          if (text.includes("success")) {
            Swal.fire({
              title: "Deleted!",
              text: "The color has been deleted.",
              icon: "success",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: text,
              icon: "error",
            });
          }
        }
      };
      request.open("POST", "deleteColor.php", true);
      request.send(form);
    }
  });
}

function addModel() {
  var mname = document.getElementById("mname").value;

  var form = new FormData();
  form.append("mname", mname);

  var request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var text = request.responseText;

      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "Model added successfully.",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.reload(); // Reload the page to see the new model
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Oops...",
          text: text,
        });
      }
    }
  };
  request.open("POST", "addModel.php", true);
  request.send(form);
}

/**
 * Asks for confirmation and deletes a model using SweetAlert2.
 * @param {number} modelId - The ID of the model to delete.
 * @param {string} modelName - The name of the model for the confirmation dialog.
 */
function deleteModel(modelId, modelName) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert the deletion of '" + modelName + "'!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      var form = new FormData();
      form.append("modelId", modelId);

      var request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          var text = request.responseText;

          if (text.includes("success")) {
            Swal.fire({
              title: "Deleted!",
              text: "The model has been deleted.",
              icon: "success",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              title: "Error!",
              text: text,
              icon: "error",
            });
          }
        }
      };
      request.open("POST", "deleteModel.php", true);
      request.send(form);
    }
  });
}

function addProduct() {
  // Grab fields
  const name = document.getElementById("pName").value;
  const desc = document.getElementById("pDesc").value;
  const price = document.getElementById("pPrice").value;
  const category = document.getElementById("pCategory").value;
  const brand = document.getElementById("pBrand").value;
  const stock = document.getElementById("pStock").value;
  const status = document.getElementById("pStatus").value;
  const files = document.getElementById("imgUpload").files;

  // Collect selected color IDs
  const colorIds = [];
  document.querySelectorAll(".color-swatch-input:checked").forEach((cb) => {
    colorIds.push(cb.value);
  });



  // Build FormData (no <form> tag required)
  const fd = new FormData();
  fd.append("pName", name);
  fd.append("pDesc", desc);
  fd.append("pPrice", price);
  fd.append("pCategory", category);
  fd.append("pBrand", brand);
  fd.append("pStock", stock);
  fd.append("pStatus", status);

  // Colors as array: pColor[]
  colorIds.forEach((id) => fd.append("pColor[]", id));

  // Images (multiple)
  for (let i = 0; i < files.length; i++) {
    fd.append("images[]", files[i]); // PHP: $_FILES['images']
  }

  // POST to your PHP endpoint (create this file)
  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      const res = xhr.responseText || "";
      if (xhr.status === 200 && res.trim() === "success") {
        Swal.fire({
          icon: "success",
          title: "Saved",
          text: "Product created successfully.",
        }).then(() => {
          // optional: close modal + refresh
          const modalEl = document.getElementById("addProductModal");
          const modal = bootstrap.Modal.getInstance(modalEl);
          modal && modal.hide();
          window.location.reload();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Failed",
          text: res || "Server error",
        });
      }
    }
  };
  xhr.open("POST", "addProduct.php", true);
  xhr.send(fd);
}


/* -------------------- Open & populate Edit Product modal -------------------- */

function openEditProduct(id){
  var f = new FormData();
  f.append("id", id);

  // === PATH: set this to where your PHP file actually is ===
  // If getProductSimple.php is at /flydolk/getProductSimple.php:
  var url = "getProduct.php";
  // If it’s inside /flydolk/admin/ use:
  // var url = "admin/getProductSimple.php";
  // Or if it’s in the same folder as this page:
  // var url = "./getProductSimple.php";
  // ========================================================

  var r = new XMLHttpRequest();
  r.onreadystatechange = function(){
    if(r.readyState === 4){
      if(r.status === 200){
        let res;
        try { res = JSON.parse(r.responseText || "{}"); }
        catch(e){
          Swal.fire({icon:"error", title:"Load failed", text:"Invalid JSON"});
          return;
        }
        if(!res.success || !res.data){
          Swal.fire({icon:"error", title:"Load failed", text: res.message || "Unknown error"});
          return;
        }

        const p = res.data;
        document.getElementById("epId").value       = p.id ?? "";
        document.getElementById("epName").value     = p.title ?? "";
        document.getElementById("epDesc").value     = p.description ?? "";
        document.getElementById("epPrice").value    = p.price ?? "";
        document.getElementById("epStock").value    = p.qty ?? "0";
        document.getElementById("epCategory").value = p.category_id ?? "0";
        document.getElementById("epBrand").value    = p.brand_id ?? "0";
        document.getElementById("epStatus").value   = p.status_id ?? "0";

        const picked = Array.isArray(p.colors) ? p.colors.map(String) : [];
        document.querySelectorAll("#epColorGrid .color-swatch-input").forEach(cb=>{
          cb.checked = picked.includes(cb.value);
        });

        new bootstrap.Modal(document.getElementById("editProductModal")).show();
      } else {
        Swal.fire({icon:"error", title:"Server Error", text:`${r.status} ${r.statusText}`});
      }
    }
  };
  r.open("POST", url, true);
  r.send(f);
}


// ---- Update Product (simple, robust) ----
function updateProductSimple(){
  const id    = document.getElementById("epId").value.trim();
  const title = document.getElementById("epName").value.trim();
  const desc  = document.getElementById("epDesc").value.trim();
  let   price = document.getElementById("epPrice").value.trim();
  price = price.replace(/[^\d.]/g, ""); // strip LKR/commas/anything non-numeric

  const qty   = (document.getElementById("epStock").value || "0").trim();
  const cat   = (document.getElementById("epCategory").value || "0").trim();
  const brand = (document.getElementById("epBrand").value || "0").trim();
  const stat  = (document.getElementById("epStatus").value || "0").trim();

  const colors = Array.from(
    document.querySelectorAll("#epColorGrid .color-swatch-input:checked")
  ).map(cb => cb.value).join(",");

  // Basic validation
  const errs = [];
  if (!id)    errs.push("Missing product id.");
  if (!title) errs.push("Product name is required.");
  if (price === "") errs.push("Price is required.");

  if (errs.length){
    Swal.fire({
      icon: "error",
      title: "Fix these",
      html: "<ul style='text-align:left;margin:0;padding-left:1rem;'>" + errs.map(e=>`<li>${e}</li>`).join("") + "</ul>"
    });
    return;
  }

  // Build request
  const f = new FormData();
  f.append("id", id);
  f.append("title", title);
  f.append("description", desc);
  f.append("price", price);
  f.append("qty", qty);
  f.append("category_id", cat);
  f.append("brand_id", brand);
  f.append("status_id", stat);
  f.append("colors", colors);

  // Adjust path if your PHP lives elsewhere
  const url = "updateProduct.php";

  // Disable the save button while sending
  const saveBtn = document.querySelector('[onclick="updateProductSimple()"]');
  if (saveBtn){ saveBtn.disabled = true; saveBtn.dataset._orig = saveBtn.innerHTML; saveBtn.innerHTML = "Saving…"; }

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function(){
    if (xhr.readyState === 4){
      if (saveBtn){ saveBtn.disabled = false; saveBtn.innerHTML = saveBtn.dataset._orig || "Save changes"; }

      if (xhr.status === 200){
        const txt = (xhr.responseText || "").trim();

        // Accept either plain "success" or JSON {success:true}
        let ok = (txt === "success");
        if (!ok && /^[{\[]/.test(txt)) {
          try { const j = JSON.parse(txt); ok = !!j.success; } catch(e){}
        }

        if (ok){
          Swal.fire({icon:"success", title:"Updated", timer:1200, showConfirmButton:false})
              .then(()=> location.reload());
        } else {
          Swal.fire({icon:"error", title:"Update failed", html: txt || "Unknown error"});
        }
      } else {
        const body = (xhr.responseText || "").slice(0, 800)
          .replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
        Swal.fire({
          icon:"error",
          title:`Server error ${xhr.status}`,
          html:`<pre style="white-space:pre-wrap;text-align:left;margin:0">${body || "(no response body)"}</pre>`
        });
      }
    }
  };
  xhr.open("POST", url, true);
  xhr.send(f);
}

// REPLACE your current deleteProduct with this
function deleteProduct(productId, productTitle) {
  Swal.fire({
    title: "Are you sure?",
    text: "Delete product '" + (productTitle || "this product") + "'?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!"
  }).then((result) => {
    if (!result.isConfirmed) return;

    var form = new FormData();
    form.append("productId", productId);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
      if (request.readyState === 4) {
        var resp = (request.responseText || "").trim();

        if (request.status === 200 && resp.indexOf("success") !== -1) {
          Swal.fire("Deleted!", "Product has been deleted.", "success")
            .then(() => window.location.reload());
        } else {
          Swal.fire("Error!", resp || ("HTTP " + request.status), "error");
        }
      }
    };
    request.open("POST", "deleteProduct.php", true);
    request.send(form);
  });
}



