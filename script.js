/* ===== Header behavior ===== */

// ====== Auth: Sign Up ======
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
        Swal.fire({ icon: "error", title: "Oops...", text: text });
      }
    }
  };
  request.open("POST", "signUpProcess.php", true);
  request.send(form);
}

// ====== Auth: Sign In ======
function signin() {
  var e = document.getElementById("e").value;
  var p = document.getElementById("p").value;

  var form = new FormData();
  form.append("e", e);
  form.append("p", p);

  var request = new XMLHttpRequest();
  request.onreadystatechange = function () {
    if (request.readyState == 4 && request.status == 200) {
      var text = request.responseText;
      if (text == "success") {
        Swal.fire({
          icon: "success",
          title: "Success!",
          text: "You have signed in successfully.",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.href = "index.php";
        });
      } else {
        Swal.fire({ icon: "error", title: "Oops...", text: text });
      }
    }
  };
  request.open("POST", "signInProcess.php", true);
  request.send(form);
}

// ====== Admin Login UI bits ======
document.addEventListener("DOMContentLoaded", function () {
  const togglePassword = document.getElementById("togglePassword");
  const passwordInput = document.getElementById("adminPassword");

  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
      const type =
        passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
      this.classList.toggle("fa-eye");
      this.classList.toggle("fa-eye-slash");
    });
  }
});

function loginAdmin() {
  var ae = document.getElementById("adminEmail").value;
  var ap = document.getElementById("adminPassword").value;

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
          window.location.href = "admin-dashboard.php";
        });
      } else {
        Swal.fire({ icon: "error", title: "Oops...", text: text });
      }
    }
  };
  request.open("POST", "adminLoginProcess.php", true);
  request.send(form);
}

// ====== Category ======
function addCategory() {
  var cname = document.getElementById("category").value;

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
          window.location.reload();
        });
      } else {
        Swal.fire({ icon: "error", title: "Oops...", text: text });
      }
    }
  };
  request.open("POST", "addCategory.php", true);
  request.send(form);
}

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
    if (result.isConfirmed) {
      var form = new FormData();
      form.append("categoryId", categoryId);

      var request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          var text = request.responseText;
          if (text.includes("success")) {
            Swal.fire({
              title: "Deleted!",
              text: "The category has been deleted.",
              icon: "success",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({ title: "Error!", text: text, icon: "error" });
          }
        }
      };
      request.open("POST", "deleteCategory.php", true);
      request.send(form);
    }
  });
}

// ====== Brand ======
function addBrand() {
  var bname = document.getElementById("bname").value;

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
          window.location.reload();
        });
      } else {
        Swal.fire({ icon: "error", title: "Oops...", text: text });
      }
    }
  };
  request.open("POST", "addBrand.php", true);
  request.send(Form);
}

function deleteBrand(brandId, brandName) {
  Swal.fire({
    title: "Are you sure?",
    text: "You won't be able to revert the deletion of '" + brandName + "'!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      var form = new FormData();
      form.append("brandId", brandId);

      var request = new XMLHttpRequest();
      request.onreadystatechange = function () {
        if (request.readyState == 4 && request.status == 200) {
          var text = request.responseText;
          if (text.includes("success")) {
            Swal.fire({
              title: "Deleted!",
              text: "The brand has been deleted.",
              icon: "success",
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({ title: "Error!", text: text, icon: "error" });
          }
        }
      };
      request.open("POST", "deleteBrand.php", true);
      request.send(form);
    }
  });
}

// ====== Color ======
function addColor() {
  var cname = document.getElementById("cname").value;

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
          window.location.reload();
        });
      } else {
        Swal.fire({ icon: "error", title: "Oops...", text: text });
      }
    }
  };
  request.open("POST", "addColor.php", true);
  request.send(form);
}

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
            Swal.fire({ title: "Error!", text: text, icon: "error" });
          }
        }
      };
      request.open("POST", "deleteColor.php", true);
      request.send(form);
    }
  });
}

// ====== Model ======
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
          window.location.reload();
        });
      } else {
        Swal.fire({ icon: "error", title: "Oops...", text: text });
      }
    }
  };
  request.open("POST", "addModel.php", true);
  request.send(form);
}

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
            Swal.fire({ title: "Error!", text: text, icon: "error" });
          }
        }
      };
      request.open("POST", "deleteModel.php", true);
      request.send(form);
    }
  });
}

/* ================== Product: Add ================== */
function addProduct() {
  // 1) Sync custom editor HTML -> hidden textarea
  const ed = document.getElementById("pDescEditor");
  if (ed && ed._syncToHidden) ed._syncToHidden();

  // 2) Grab fields
  const name = document.getElementById("pName").value;
  const desc = document.getElementById("pDesc").value; // now contains HTML
  const price = document.getElementById("pPrice").value;
  const category = document.getElementById("pCategory").value;
  const brand = document.getElementById("pBrand").value;
  const stock = document.getElementById("pStock").value;
  const status = document.getElementById("pStatus").value;
  const files = document.getElementById("imgUpload").files;

  // Colors
  const colorIds = [];
  document
    .querySelectorAll(".color-swatch-input:checked")
    .forEach((cb) => colorIds.push(cb.value));

  // 3) Build FormData
  const fd = new FormData();
  fd.append("pName", name);
  fd.append("pDesc", desc); // HTML from custom editor
  fd.append("pPrice", price);
  fd.append("pCategory", category);
  fd.append("pBrand", brand);
  fd.append("pStock", stock);
  fd.append("pStatus", status);
  colorIds.forEach((id) => fd.append("pColor[]", id));
  for (let i = 0; i < files.length; i++) fd.append("images[]", files[i]);

  // 4) Send
  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      const res = (xhr.responseText || "").trim();
      if (xhr.status === 200 && res === "success") {
        Swal.fire({
          icon: "success",
          title: "Saved",
          text: "Product created successfully.",
        }).then(() => {
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

/* ================== Product: Edit ================== */
function openEditProduct(id) {
  var f = new FormData();
  f.append("id", id);

  var url = "getProduct.php"; // adjust if your path differs

  var r = new XMLHttpRequest();
  r.onreadystatechange = function () {
    if (r.readyState === 4) {
      if (r.status === 200) {
        let res;
        try {
          res = JSON.parse(r.responseText || "{}");
        } catch (e) {
          Swal.fire({
            icon: "error",
            title: "Load failed",
            text: "Invalid JSON",
          });
          return;
        }
        if (!res.success || !res.data) {
          Swal.fire({
            icon: "error",
            title: "Load failed",
            text: res.message || "Unknown error",
          });
          return;
        }

        const p = res.data;
        document.getElementById("epId").value = p.id ?? "";
        document.getElementById("epName").value = p.title ?? "";
        document.getElementById("epPrice").value = p.price ?? "";
        document.getElementById("epStock").value = p.qty ?? "0";
        document.getElementById("epCategory").value = p.category_id ?? "0";
        document.getElementById("epBrand").value = p.brand_id ?? "0";
        document.getElementById("epStatus").value = p.status_id ?? "0";

        // Fill the visible editor with HTML (and ensure hidden matches)
        const editor = document.getElementById("epDescEditor");
        if (editor) editor.innerHTML = p.description || "";
        const hidden = document.getElementById("epDesc");
        if (hidden) hidden.value = p.description || "";

        const picked = Array.isArray(p.colors) ? p.colors.map(String) : [];
        document
          .querySelectorAll("#epColorGrid .color-swatch-input")
          .forEach((cb) => {
            cb.checked = picked.includes(cb.value);
          });

        new bootstrap.Modal(document.getElementById("editProductModal")).show();
      } else {
        Swal.fire({
          icon: "error",
          title: "Server Error",
          text: `${r.status} ${r.statusText}`,
        });
      }
    }
  };
  r.open("POST", url, true);
  r.send(f);
}

function updateProductSimple() {
  // Sync edit editor to hidden field
  const ed = document.getElementById("epDescEditor");
  if (ed && ed._syncToHidden) ed._syncToHidden();

  const id = document.getElementById("epId").value.trim();
  const title = document.getElementById("epName").value.trim();
  const desc = document.getElementById("epDesc").value.trim(); // HTML from custom editor
  let price = document.getElementById("epPrice").value.trim();
  price = price.replace(/[^\d.]/g, "");

  const qty = (document.getElementById("epStock").value || "0").trim();
  const cat = (document.getElementById("epCategory").value || "0").trim();
  const brand = (document.getElementById("epBrand").value || "0").trim();
  const stat = (document.getElementById("epStatus").value || "0").trim();

  const colors = Array.from(
    document.querySelectorAll("#epColorGrid .color-swatch-input:checked")
  )
    .map((cb) => cb.value)
    .join(",");

  const errs = [];
  if (!id) errs.push("Missing product id.");
  if (!title) errs.push("Product name is required.");
  if (price === "") errs.push("Price is required.");
  if (errs.length) {
    Swal.fire({
      icon: "error",
      title: "Fix these",
      html:
        "<ul style='text-align:left;margin:0;padding-left:1rem;'>" +
        errs.map((e) => `<li>${e}</li>`).join("") +
        "</ul>",
    });
    return;
  }

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

  const url = "updateProduct.php";

  const saveBtn = document.querySelector('[onclick="updateProductSimple()"]');
  if (saveBtn) {
    saveBtn.disabled = true;
    saveBtn.dataset._orig = saveBtn.innerHTML;
    saveBtn.innerHTML = "Saving…";
  }

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      if (saveBtn) {
        saveBtn.disabled = false;
        saveBtn.innerHTML = saveBtn.dataset._orig || "Save changes";
      }

      if (xhr.status === 200) {
        const txt = (xhr.responseText || "").trim();
        let ok = txt === "success";
        if (!ok && /^[{\[]/.test(txt)) {
          try {
            ok = !!JSON.parse(txt).success;
          } catch (e) {}
        }

        if (ok) {
          Swal.fire({
            icon: "success",
            title: "Updated",
            timer: 1200,
            showConfirmButton: false,
          }).then(() => location.reload());
        } else {
          Swal.fire({
            icon: "error",
            title: "Update failed",
            html: txt || "Unknown error",
          });
        }
      } else {
        const body = (xhr.responseText || "")
          .slice(0, 800)
          .replace(/&/g, "&amp;")
          .replace(/</g, "&lt;")
          .replace(/>/g, "&gt;");
        Swal.fire({
          icon: "error",
          title: `Server error ${xhr.status}`,
          html: `<pre style="white-space:pre-wrap;text-align:left;margin:0">${
            body || "(no response body)"
          }</pre>`,
        });
      }
    }
  };
  xhr.open("POST", url, true);
  xhr.send(f);
}

// ====== Delete Product ======
function deleteProduct(productId, productTitle) {
  Swal.fire({
    title: "Are you sure?",
    text: "Delete product '" + (productTitle || "this product") + "'?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (!result.isConfirmed) return;

    var form = new FormData();
    form.append("productId", productId);

    var request = new XMLHttpRequest();
    request.onreadystatechange = function () {
      if (request.readyState === 4) {
        var resp = (request.responseText || "").trim();
        if (request.status === 200 && resp.indexOf("success") !== -1) {
          Swal.fire("Deleted!", "Product has been deleted.", "success").then(
            () => window.location.reload()
          );
        } else {
          Swal.fire("Error!", resp || "HTTP " + request.status, "error");
        }
      }
    };
    request.open("POST", "deleteProduct.php", true);
    request.send(form);
  });
}

// ===== Flydolk Showcase Logic =====
/* ================== Drone Showcase ================== */
gsap.registerPlugin(ScrollTrigger);

// All available DJI models (manual list). Update filenames if needed.
const ALL_MODELS = [
  {
    name: "DJI Mavic 3 Pro",
    img: "uploads/products/Slide_DJI_Mavic_3_Pro_drone_68d56eee0d37c.png",
    price: "LKR 1,180,000",
    colors: ["Gray"],
    desc: "Flagship triple-camera (Hasselblad wide, 70mm medium tele & 166mm tele). 5.1K/50fps, omnidirectional sensing, O3+.",
  },
  {
    name: "DJI Mavic 3 Enterprise",
    img: "uploads/products/Slide_mavic-3-enterprise-removebg-preview_68d56b0393cc0.png",
    price: "LKR 1,650,000",
    colors: ["Gray"],
    desc: "Enterprise mapping & inspection platform. 56× hybrid zoom, RTK support, advanced safety & reliability.",
  },
  {
    name: "DJI Air 2S",
    img: "uploads/products/Slide_Air-2S-1_68d56d5178c05.png",
    price: "LKR 650,000",
    colors: ["Gray"],
    desc: "1-inch 20MP sensor in a compact frame. 5.4K/30, MasterShots, ADS-B AirSense, 31-min flight, O3.",
  },
  {
    name: "DJI Mavic Air 2",
    img: "uploads/products/Slide_DJI_air_2_68d565fbd5016.png",
    price: "LKR 525,000",
    colors: ["Gray"],
    desc: "48MP photos, 4K/60 video, 34-min flight, APAS 3.0. Balanced power and portability.",
  },
  {
    name: "DJI Mavic 2 Pro",
    img: "uploads/products/Slide_Mavic_2_Pro_68d56e6266e1e.png",
    price: "LKR 720,000",
    colors: ["Gray"],
    desc: "Hasselblad 1” CMOS, adjustable aperture f/2.8–f/11, Dlog-M 10-bit color, OcuSync 2.0.",
  },
  {
    name: "DJI Mavic 2 Zoom",
    img: "uploads/products/Slide_Mavic_2_Pro_68d56e6266e1e.png",
    price: "LKR 690,000",
    colors: ["Gray"],
    desc: "24–48mm optical zoom, 12MP photos, 4K video, Dolly Zoom effect, OcuSync 2.0.",
  },
  {
    name: "DJI Mavic Air 2",
    img: "uploads/products/Slide_DJI_air_2_68d565fbd5016.png",
    price: "LKR 560,000",
    colors: ["Gray"],
    desc: "Classic foldable with 4K stabilized camera and reliable range. Compact, dependable, iconic.",
  },
];

// Pick a random 5 each load
function shuffle(a) {
  for (let i = a.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [a[i], a[j]] = [a[j], a[i]];
  }
  return a;
}
const MODELS = shuffle([...ALL_MODELS]).slice(0, 5);

// Layout switches
const ARC_SIDE = "left"; // place thumbnails on left arc
const HIDE_HALF = "left"; // hide left half of ring => show RIGHT semicircle (matches your mock)

// Elements
const orbitEl = document.getElementById("orbit");
const mainImg = document.getElementById("mainDrone");
const titleEl = document.getElementById("modelTitle");
const descEl = document.getElementById("modelDesc");
const metaEl = document.getElementById("modelMeta");
const infoWrap = document.getElementById("infoWrap");
const dotsEl = document.getElementById("stepDots");
const ringEl = document.getElementById("ring");

if (ringEl) {
  ringEl.classList.remove("ring--show-left", "ring--show-right");
  // hide left half -> show right semicircle
  ringEl.classList.add(
    HIDE_HALF === "left" ? "ring--show-right" : "ring--show-left"
  );
}

let currentIndex = -1;
let thumbs = [];
let pinST;

// Build progress dots (for small screens)
MODELS.forEach((_, i) => {
  const d = document.createElement("div");
  d.className = "dot" + (i === 0 ? " active" : "");
  dotsEl.appendChild(d);
});

// Build orbit on chosen side
function buildOrbit() {
  orbitEl.innerHTML = "";
  thumbs = [];

  const W = orbitEl.clientWidth;
  const H = orbitEl.clientHeight;
  const R = Math.min(W, H) / 2 - 10;

  // Vertical semicircle: -90° (top) to +90° (bottom)
  const start = -90,
    end = 90,
    steps = MODELS.length;

  MODELS.forEach((m, i) => {
    const t = steps === 1 ? 0 : i / (steps - 1);
    const rad = ((start + (end - start) * t) * Math.PI) / 180;

    const dir = ARC_SIDE === "left" ? -1 : 1; // mirror X for left side
    const x = dir * Math.cos(rad) * R;
    const y = Math.sin(rad) * R;

    const d = document.createElement("div");
    d.className = "thumb" + (i === 0 ? " active" : "");
    const img = document.createElement("img");
    img.src = m.img;
    img.alt = m.name;
    d.appendChild(img);
    orbitEl.appendChild(d);

    requestAnimationFrame(() => {
      d.style.left = W / 2 + x - d.offsetWidth / 2 + "px";
      d.style.top = H / 2 + y - d.offsetHeight / 2 + "px";
    });

    d.addEventListener("click", () => {
      gotoIndex(i);
      if (pinST) {
        const p = i / (MODELS.length - 1);
        pinST.scroll(p * (pinST.end - pinST.start) + pinST.start);
      }
    });

    thumbs.push(d);
  });
}

// Render selected model
function renderModel(i) {
  const m = MODELS[i];

  titleEl.textContent = m.name;
  descEl.textContent = m.desc;
  metaEl.innerHTML = `
    <span class="badge text-bg-dark rounded-pill">${m.price}</span>
    ${m.colors
      .map(
        (c) => `<span class="badge text-bg-secondary rounded-pill">${c}</span>`
      )
      .join("")}
  `;

  // Animations (flipped as requested)
  // Image: right -> left
  gsap.fromTo(
    mainImg,
    { x: 60, opacity: 0 },
    { x: 0, opacity: 1, duration: 0.9, ease: "power3.out" }
  );
  mainImg.src = m.img;

  // Text: left -> right
  gsap.fromTo(
    infoWrap,
    { x: -60, opacity: 0 },
    { x: 0, opacity: 1, duration: 0.9, ease: "power3.out", delay: 0.05 }
  );

  thumbs.forEach((t, idx) => t.classList.toggle("active", idx === i));
  [...dotsEl.children].forEach((d, idx) =>
    d.classList.toggle("active", idx === i)
  );
}

function gotoIndex(i) {
  if (i !== currentIndex) {
    currentIndex = i;
    renderModel(i);
  }
}

// Init
window.addEventListener("load", () => {
  buildOrbit();
  gotoIndex(0);
});
window.addEventListener("resize", buildOrbit);

// Pin & scrub across models
pinST = ScrollTrigger.create({
  trigger: "#showcasePin",
  start: "top top",
  end: () => "+=" + MODELS.length * 900,
  scrub: true,
  pin: true,
  onUpdate: (self) => {
    const idx = Math.round(self.progress * (MODELS.length - 1));
    gotoIndex(idx);
  },
});

// --- push showcase below your existing header (no header CSS changes) ---
function setHeaderOffset() {
  // Try common header selectors; tweak if your header uses a different id/class
  const header = document.querySelector(
    "header, .site-header, .header, #header"
  );
  const h = header ? header.offsetHeight : 0;
  document.documentElement.style.setProperty("--header-h", `${h}px`);
}
// set once + on resize to handle responsive header heights
window.addEventListener("load", setHeaderOffset);
window.addEventListener("resize", setHeaderOffset);

/* ================== /Drone Showcase ================== */

/* ===== Header sizing + behavior ===== */
function setHeaderOffset() {
  const header = document.querySelector("header.fd-header");
  const h = header ? header.offsetHeight : 0;
  document.documentElement.style.setProperty("--header-h", `${h}px`);
}
window.addEventListener("load", setHeaderOffset);
window.addEventListener("resize", setHeaderOffset);

(function () {
  const header = document.querySelector("header.fd-header");
  if (!header) return;
  const toggle = () =>
    header.classList.toggle("is-scrolled", window.scrollY > 8);
  window.addEventListener("scroll", toggle, { passive: true });
  toggle();
})();

/* ===== Footer utilities ===== */
(function fdClockTick() {
  const el = document.getElementById("fd-clock");
  if (!el) return;
  el.textContent = new Date().toLocaleTimeString();
  setTimeout(fdClockTick, 1000);
})();
(function fdSetYear() {
  const y = document.getElementById("fd-year");
  if (y) y.textContent = new Date().getFullYear();
})();
function fdBackToTop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}
function fdSubscribeNews() {
  const i = document.getElementById("fd-news-email");
  if (!i) return;
  const v = (i.value || "").trim();
  const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
  if (!ok) {
    window.Swal
      ? Swal.fire({
          icon: "warning",
          title: "Invalid email",
          text: "Please enter a valid email.",
        })
      : alert("Please enter a valid email.");
    return;
  }
  // TODO: POST to newsletterSubscribe.php
  window.Swal
    ? Swal.fire({
        icon: "success",
        title: "Subscribed!",
        text: "Welcome aboard 🛫",
      })
    : alert("Subscribed!");
  i.value = "";
}
/* ===== End Footer utilities ===== */

/* ===== Shop page tweaks ===== */

