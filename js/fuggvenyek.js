function generateOptions() {
  return new Promise((resolve, reject) => {
    $.ajax({
      method: "post",
      url: "source_reading.php",
      data: {
        data: "szamlatukor",
      },
      success: function (response) {
        const dataOptions = JSON.parse(response);
        resolve(dataOptions);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        reject(new Error("AJAX request failed"));
        console.log(textStatus, errorThrown);
      },
    });
  });
}

let lineCount = 1;
function addNewLine() {
  lineCount++;

  let newRow = $("#inputRow_1").clone();
  newRow.attr("id", "inputRow_" + lineCount);

  newRow
    .find('[id^="tartozik_"]')
    .attr("id", "tartozik_" + lineCount)
    .val("");
  newRow
    .find('[name^="tartozik_"]')
    .attr("name", "tartozik_" + lineCount)
    .val("");
  newRow
    .find('[id^="kovetel_"]')
    .attr("id", "kovetel_" + lineCount)
    .val("");
  newRow
    .find('[name^="kovetel_"]')
    .attr("name", "kovetel_" + lineCount)
    .val("");
  newRow
    .find('[id^="osszeg_"]')
    .attr("id", "osszeg_" + lineCount)
    .val("");
  newRow
    .find('[name^="osszeg_"]')
    .attr("name", "osszeg_" + lineCount)
    .val("");
  newRow
    .find('[id^="trash_"]')
    .attr("id", "trash_" + lineCount)
    .val("");

  $("#dynamicInputSection").append(newRow);
}

function addNewLineBank() {
  lineCount++;

  let newRow = $("#inputRow_1").clone();
  newRow.attr("id", "inputRow_" + lineCount);

  newRow
    .find('[id^="bank_datum_"]')
    .attr("id", "bank_datum_" + lineCount)
    .val("");
  newRow
    .find('[name^="bank_datum_"]')
    .attr("name", "bank_datum_" + lineCount)
    .val("");

  newRow.find('[id^="szamlak_"]').attr("id", "szamlak_" + lineCount);
  newRow.find('[name^="szamlak_"]').attr("name", "szamlak_" + lineCount);
  $("#szamlak_" + lineCount).prop("selectedIndex", 0);

  newRow
    .find('[id^="tartozik_"]')
    .attr("id", "tartozik_" + lineCount)
    .val("");
  newRow
    .find('[name^="tartozik_"]')
    .attr("name", "tartozik_" + lineCount)
    .val("");

  newRow
    .find('[id^="kovetel_"]')
    .attr("id", "kovetel_" + lineCount)
    .val("");
  newRow
    .find('[name^="kovetel_"]')
    .attr("name", "kovetel_" + lineCount)
    .val("");

  newRow
    .find('[id^="osszeg_"]')
    .attr("id", "osszeg_" + lineCount)
    .val("");
  newRow
    .find('[name^="osszeg_"]')
    .attr("name", "osszeg_" + lineCount)
    .val("");

  newRow
    .find('[id^="trash_"]')
    .attr("id", "trash_" + lineCount)
    .val("");

  $("#dynamicInputSection").append(newRow);
}

function calculateTotal() {
  let total = 0;
  let osszeg = document.getElementById("osszeg").value;
  let button = document.getElementById("konyvmentes");

  let tkcount = 0;
  $('[id^="osszeg_"]').each(function () {
    let tartozik = $('#tartozik_' + tkcount).val();
    let kovetel = $('#kovetel_' + tkcount).val();
    let value = 0;
    switch (true) {
      case kovetel.startsWith("45"):
        value = parseFloat($(this).val()) || 0;
        total += value;
        break;
      case tartozik.startsWith("45"):
        value = parseFloat($(this).val()) || 0;
        total -= value;
        break;
      case kovetel.startsWith("9"):
        value = parseFloat($(this).val()) || 0;
        total += value;
        break;
      case tartozik.startsWith("9"):
        value = parseFloat($(this).val()) || 0;
        total -= value;
        break;
      case kovetel.startsWith("381"):
        value = parseFloat($(this).val()) || 0;
        total -= value;
        break;
      case tartozik.startsWith("381"):
        value = parseFloat($(this).val()) || 0;
        total += value;
        break;
      default:
        value = parseFloat($(this).val()) || 0;
        total += value;
        break;
    }
    tkcount++;
  });

  if (total < 0) {
    total = total * -1;
  }

  if (osszeg == total) {
    button.disabled = false;
  } else {
    button.disabled = true;
  }

  $("#totalInput").val(total);
}

function calculateTotalEgyeb() {
  let total = 0;
  let tkcount = 0;

  $('[id^="osszeg_"]').each(function () {
    let tartozik = $('#tartozik_' + tkcount).val();
    let kovetel = $('#kovetel_' + tkcount).val();
    let value = 0;
    switch (true) {
      case kovetel.startsWith("45"):
        value = parseFloat($(this).val()) || 0;
        total += value;
        break;
      case tartozik.startsWith("45"):
        value = parseFloat($(this).val()) || 0;
        total -= value;
        break;
      case kovetel.startsWith("9"):
        value = parseFloat($(this).val()) || 0;
        total += value;
        break;
      case tartozik.startsWith("9"):
        value = parseFloat($(this).val()) || 0;
        total -= value;
        break;
      case kovetel.startsWith("381"):
        value = parseFloat($(this).val()) || 0;
        total -= value;
        break;
      case tartozik.startsWith("381"):
        value = parseFloat($(this).val()) || 0;
        total += value;
        break;
      default:
        value = parseFloat($(this).val()) || 0;
        total += value;
        break;
    }
    tkcount++;
  });

  $("#totalInput").val(total);
}

function getFormData(form) {
  var unindexed_array = form.serializeArray();
  var indexed_array = {};

  $.map(unindexed_array, function (n, i) {
    indexed_array[n["name"]] = n["value"];
  });

  return indexed_array;
}

let actualLine = 0;
function loadSablon() {
  let url = "inputkezeles.php";
  $.ajax({
    method: "post",
    url: url,
    data: {
      sablon: document.getElementById("sablon").value,
    },
    success: function (response) {
      r = JSON.parse(response);
      if (r != "no") {
        let talaltunk_ureset = false;
        actualLine = 0;
        while (!talaltunk_ureset) {
          if (document.getElementById("tartozik_" + actualLine)) {
            if (
              document.getElementById("tartozik_" + actualLine).value.length >
              0 ||
              document.getElementById("kovetel_" + actualLine).value.length > 0
            ) {
              actualLine++;
            } else {
              document.getElementById("tartozik_" + actualLine).value =
                r["tartozik"];
              document.getElementById("kovetel_" + actualLine).value =
                r["kovetel"];
              actualLine++;
              $("#sablon").prop("selectedIndex", 0);
              talaltunk_ureset = true;
            }
          } else {
            addNewLine();
            document.getElementById("tartozik_" + actualLine).value =
              r["tartozik"];
            document.getElementById("kovetel_" + actualLine).value =
              r["kovetel"];
            $("#sablon").prop("selectedIndex", 0);
            talaltunk_ureset = true;
          }
        }
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    },
  });
}

function addingNewClient() {
  let text;
  let person = prompt("Új partner rögzítése:", "");
  if (person == null || person == "") {
    text = "User cancelled the prompt.";
  } else {
    text = "Hello " + person + "! How are you today?";
  }
}

function savePartner() {
  $.ajax({
    method: "post",
    url: "inputkezeles.php",
    data: {
      "partnernev": $("#partner_nev").val(),
      "partneradoszam": $("#partner_adoszam").val(),
      "vevo": $("#vevo_azonosito").is(':checked')
    },
    success: function (response) {
      if (response == "nem") {
        $("#alertbox").addClass("alert-danger");
        $("#alertbox").text("Sikertelen rögzítés");
        $("#alertbox").removeClass("hidden");
        $("#bezaras").text("Bezárás");
      } else {
        if (response == "hibasadoszam") {
          $("#alertbox").addClass("alert-danger");
          $("#alertbox").text("Hibás adószám!");
          $("#alertbox").removeClass("hidden");
          $("#bezaras").text("Bezárás");
        } else {
          $("#alertbox").addClass("alert-success");
          $("#alertbox").text("Sikeres rögzítés");
          $("#alertbox").removeClass("hidden");
          $("#alertbox").removeClass("alert-danger");
          $("#bezaras").text("Bezárás");
          setTimeout(() => {
            $("#partner_nev").val("");
            $("#partner_adoszam").val("");
            $("#alertbox").addClass("hidden");
          }, 1000);
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        }
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    },
  });
}

function hiba(uzenet, alertboxid) {
  $("#" + alertboxid).removeClass("alert-success");
  $("#" + alertboxid).addClass("alert-danger");
  $("#" + alertboxid).text(uzenet);
  $("#" + alertboxid).removeClass("hidden");
}

function kereses(inputfield, tabla) {
  let input, filter, table, tr, td, i, txtValue, inputValue;
  input = document.getElementById(inputfield);
  filter = input.value.toUpperCase();
  table = document.getElementById(tabla);
  tr = table.getElementsByTagName("tr");

  if (tabla == "szallitoszamlak") {
    if (inputfield == "szallito_nev") {
      for (i = 0; i < tr.length; i++) {
        /* első oszlopban keres */
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    } else {
      for (i = 0; i < tr.length; i++) {
        /* második oszlopban keres */
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }
  }
  if (tabla == "vevoszamlak") {
    if (inputfield == "vevo_nev") {
      for (i = 0; i < tr.length; i++) {
        /* első oszlopban keres */
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    } else {
      for (i = 0; i < tr.length; i++) {
        /* második oszlopban keres */
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }
  }
  if (tabla == "rogzitettpenztar") {
    if (inputfield == "rogzitettpenztar_nev") {
      for (i = 0; i < tr.length; i++) {
        /* első oszlopban keres */
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    } else {
      for (i = 0; i < tr.length; i++) {
        /* második oszlopban keres */
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }
  }

  if (tabla == "penztar") {
    for (i = 0; i < tr.length; i++) {
      /* második oszlopban keres */
      td = tr[i].getElementsByTagName("td")[1];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  if (tabla == "bank") {
    for (i = 0; i < tr.length; i++) {
      /* harmadik oszlopban keres */
      td = tr[i].getElementsByTagName("td")[2];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  if (tabla == "egyeb") {
    if (inputfield == "egyeb_megnevezes") {
      for (i = 0; i < tr.length; i++) {
        /* második oszlopban keres */
        td = tr[i].getElementsByTagName("td")[1];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    } else {
      for (i = 0; i < tr.length; i++) {
        /* harmadik oszlopban keres */
        td = tr[i].getElementsByTagName("td")[2];
        if (td) {
          txtValue = td.textContent || td.innerText;
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
          } else {
            tr[i].style.display = "none";
          }
        }
      }
    }
  }
  if (tabla == "tlista") {
    for (i = 0; i < tr.length; i++) {
      /* első oszlopban keres */
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }
  if (tabla == "fokonyviszamok") {
    for (i = 0; i < tr.length; i++) {
      /* első oszlopban keres */
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }
  if (tabla == "partnertable") {
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        var inputs = td.getElementsByTagName("input");
        if (inputs.length > 0) {
          inputValue = inputs[0].value;
          if (inputValue) {
            if (inputValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }
        }
      }
    }
  }
}

function szamlamodositas(id) {
  let tmp = id.split("_");
  let szamlatipus = tmp[0];
  let muvelet = tmp[1];
  let szamlaszam = tmp[2];
  let vegzendomuvelet = muvelet;
  let url = "../szallito_vevo_penztar_szamlamodositas.php";
  if (muvelet == "del") {
    $.ajax({
      method: "post",
      url: "../szamlamodositas.php",
      data: {
        "modositas": szamlaszam,
        "szamlatipus": szamlatipus,
        "vegzendomuvelet": vegzendomuvelet
      },
      success: function (response) {
        if (response == "sikeresszamlatorles") {
          switch (szamlatipus) {
            case 'szallito':
              window.location.replace('szallitolist.php');
              break;
            case 'vevo':
              window.location.replace('vevolist.php');
              break;
            case 'penztar':
              window.location.replace('penztarlist.php');
              break;
            case 'penztarszamla':
              window.location.replace('penztarlist.php');
              break;
            case 'bank':
              window.location.replace('banklist.php');
              break;
            case 'egyeb':
              window.location.replace('egyeblist.php');
              break;
          };
        } else {
          console.log(response);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert("Hiba");
        console.log(textStatus, errorThrown);
      },
    });
  } else {
    $.ajax({
      method: "post",
      url: "../szamlamodositas.php",
      data: {
        "modositas": szamlaszam,
        "szamlatipus": szamlatipus,
        "vegzendomuvelet": vegzendomuvelet
      },
      success: function (response) {
        if (response == "sikeresszamlalekeres") {
          window.location.replace(url);
        }
        if (response == "sikeresbank" || response == "sikeresegyeb") {
          window.location.replace("../bank_egyeb_modositas.php");
        }
        else {
          console.log(response);
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert("Hiba");
        console.log(textStatus, errorThrown);
      },
    });
  }

}

function pickinvoice(invoice) {
  var url = 'upload_process.php';
  $.ajax({
    method: 'post',
    url: url,
    data: {
      "invoice": invoice.getAttribute("value")
    },
    success: function (response) {
      window.location.href = "szamlarogzites.php";
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });
}

function pickall() {
  var url = 'upload_process.php';
  $.ajax({
    method: 'post',
    url: url,
    data: {
      "invoice": "all"
    },
    success: function (response) {
      window.location.href = "szamlarogzites.php";
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });
}

function picknone() {
  var url = 'upload_process.php';
  $.ajax({
    method: 'post',
    url: url,
    data: {
      "invoice": "none"
    },
    success: function (response) {
      window.location.href = "szamlarogzites.php";
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });
}

function collectUjCegAdatok() {
  let form = $("#cegrogzites_form");
  let forms = [];
  forms[0] = getFormData(form);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "ujcegdata": forms
    },
    success: function (response) {
      console.log(response);
      if (response == "cegnevhiany") {
        hiba("Név megadása kötelező!", "alertbox_ceg");
      }
      if (response == "ceghibasadoszam") {
        hiba("Hibás adószám!", "alertbox_ceg");
      }
      if (response == "cegadoszamhiany") {
        hiba("Adószám megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegelerhetoseghiba") {
        hiba("Elérhetőség megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegirszhiba") {
        hiba("Irányítószám megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegvaroshiba") {
        hiba("Város megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegkozterhiba") {
        hiba("Közterület megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegkozterjelleghiba") {
        hiba("Közterület jellege megadása kötelező!", "alertbox_ceg");
      }
      if (response == "ceghazszamepulethiba") {
        hiba("Házszám megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegafahiba") {
        hiba("ÁFA kiválasztása kötelező!", "alertbox_ceg");
      }
      if (response == "siker") {
        if ($('#alertbox_ceg').hasClass("hidden")) {
          $('#alertbox_ceg').removeClass("hidden");
        } else {
          $('#alertbox_ceg').removeClass("alert-danger");
          $('#alertbox_ceg').addClass("alert-success");
          $('#alertbox_ceg').text("Sikeres rögzítés");
        }
        setTimeout(() => {
          $('#alertbox_ceg').addClass("hidden");
        }, 1000);
        setTimeout(() => {

          window.location.replace("cegrogzites.php");


        }, 1000);
      }

    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });
}

function collectModCegAdatok() {
  let form = $("#cegrogzites_form");
  let forms = [];
  forms[0] = getFormData(form);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "ujcegdata": forms,
      "muvelet": "mod"
    },
    success: function (response) {
      console.log(response);
      if (response == "cegnevhiany") {
        hiba("Név megadása kötelező!", "alertbox_ceg");
      }
      if (response == "ceghibasadoszam") {
        hiba("Hibás adószám!", "alertbox_ceg");
      }
      if (response == "cegadoszamhiany") {
        hiba("Adószám megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegelerhetoseghiba") {
        hiba("Elérhetőség megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegirszhiba") {
        hiba("Irányítószám megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegvaroshiba") {
        hiba("Város megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegkozterhiba") {
        hiba("Közterület megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegkozterjelleghiba") {
        hiba("Közterület jellege megadása kötelező!", "alertbox_ceg");
      }
      if (response == "ceghazszamepulethiba") {
        hiba("Házszám megadása kötelező!", "alertbox_ceg");
      }
      if (response == "cegafahiba") {
        hiba("ÁFA kiválasztása kötelező!", "alertbox_ceg");
      }
      if (response == "siker") {
        if ($('#alertbox_ceg').hasClass("hidden")) {
          $('#alertbox_ceg').removeClass("hidden");
        } else {
          $('#alertbox_ceg').removeClass("alert-danger");
          $('#alertbox_ceg').addClass("alert-success");
          $('#alertbox_ceg').text("Sikeres rögzítés");
        }
        setTimeout(() => {
          $('#alertbox_ceg').addClass("hidden");
        }, 1000);
        setTimeout(() => {

          window.location.replace("cegek.php");


        }, 1000);
      }

    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });
}

function deleteTetel(id) {
  let tmp = id.split("_");
  console.log(id);
  if (tmp[1] !== '0' && tmp[1] !== '1') {
    $('#tartozik_' + tmp[1]).val(0);
    $('#kovetel_' + tmp[1]).val(0);
    $('#osszeg_' + tmp[1]).val(0);
    $('#' + id).closest('.row').remove();
    calculateTotal();
  } else {
    $('#tartozik_' + tmp[1]).val(0);
    $('#kovetel_' + tmp[1]).val(0);
    $('#osszeg_' + tmp[1]).val(0);
    calculateTotal();
  }


}
function cegModositas(id) {
  $.ajax({
    method: 'post',
    url: "szamlamodositas.php",
    data: {
      "ceg_adoszam": id,
      "muvelet": "modositas"
    },
    success: function (response) {
      if (response == "siker") {
        window.location.replace("cegrogzites.php");
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });
}

function editPartner(id) {
  let tmp = id.split("_");
  console.log("művelet:" + tmp[0]);
  if (tmp[0] == "szerkeszt") {
    if ($('#' + id).hasClass("active_button")) {
      const adatok = [];
      adatok.push($('#nev_' + tmp[1]).val());
      adatok.push($('#adoszam_' + tmp[1]).val());
      adatok.push($('input[name="partnertipus_' + tmp[1] + '"]:checked').val());
      adatok.push($('#old_adoszam_' + tmp[1]).val());

      $.ajax({
        method: 'post',
        url: "inputkezeles.php",
        data: {
          "partneradat": adatok
        },
        success: function (response) {
          console.log(response);
          if (response == "nevMegadasKotelezo") {
            hiba("Név megadása kötelező!", "alertbox_partner");
          }
          if (response == "hibasadoszam") {
            hiba("Hibás adószám!", "alertbox_partner");
          }
          if (response == "adoszamhiany") {
            hiba("Adószám megadása kötelező!", "alertbox_partner");
          }
          if (response == "siker") {
            if ($('#alertbox_partner').hasClass("hidden")) {
              $('#alertbox_partner').removeClass("hidden");
              $('#alertbox_partner').addClass("alert-success");
              $('#alertbox_partner').text("Sikeres rögzítés");
            } else {
              $('#alertbox_partner').removeClass("alert-danger");
              $('#alertbox_partner').addClass("alert-success");
              $('#alertbox_partner').text("Sikeres rögzítés");
            }
            setTimeout(() => {
              $('#alertbox_partner').addClass("hidden");
            }, 1000);
            setTimeout(() => {
              window.location.replace("ugyfelek.php");
            }, 1000);
          }

        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert("Hiba");
          console.log(textStatus, errorThrown);
        }
      });
    } else {
      $('#nev_' + tmp[1]).removeClass("border-0");
      $('#adoszam_' + tmp[1]).removeClass("border-0");
      $('#vevo_' + tmp[1]).removeClass("border-0");
      $('#label_1_' + tmp[1]).removeClass("border-0");
      $('#tipus_1_' + tmp[1]).removeClass("border-0");
      $('#label_2_' + tmp[1]).removeClass("border-0");
      $('#tipus_2_' + tmp[1]).removeClass("border-0");

      $('#nev_' + tmp[1]).attr("disabled", false);
      $('#adoszam_' + tmp[1]).attr("disabled", false);
      $('#vevo_' + tmp[1]).attr("disabled", false);
      $('#label_1_' + tmp[1]).attr("disabled", false);
      $('#tipus_1_' + tmp[1]).attr("disabled", false);
      $('#label_2_' + tmp[1]).attr("disabled", false);
      $('#tipus_2_' + tmp[1]).attr("disabled", false);

      $('#label_1_' + tmp[1]).addClass("active_button");
      $('#label_2_' + tmp[1]).addClass("active_button");


      $('#' + id).addClass("border-info");
      $('#' + id).addClass("active_button");
      $('#' + id).html("");
      $('#' + id).html('<i class="fa-solid fa-floppy-disk"></i>');
    }
  } else {
    if (confirm("Valóban törölni szeretné a " + $('#nev_' + tmp[1]).val() + " nevű partnert?") == true) {
      $.ajax({
        method: "post",
        url: "inputkezeles.php",
        data: {
          "partnertorles": tmp[1],
        },
        success: function (response) {
          if (response == "sikertelentorles") {
            hiba("A törlés sikertelen!", alertbox_partner);
          }
          if (response == "sikerestorles") {
            if ($('#alertbox_partner').hasClass("hidden")) {
              $('#alertbox_partner').removeClass("hidden");
              $('#alertbox_partner').addClass("alert-success");
              $('#alertbox_partner').text("Sikeres törlés");
            } else {
              $('#alertbox_partner').removeClass("alert-danger");
              $('#alertbox_partner').addClass("alert-success");
              $('#alertbox_partner').text("Sikeres törlés");
            }
            setTimeout(() => {
              $('#alertbox_partner').addClass("hidden");
            }, 1000);
            setTimeout(() => {
              window.location.replace("ugyfelek.php");
            }, 1000);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert("Hiba");
          console.log(textStatus, errorThrown);
        },
      });
    }
  }
}

function saveUjKonyvelesiSablon() {

  let uj_ertekek = {
    ":nev": $("#megnevezes").val(),
    ":tartozik": $("#tartozik_0").val(),
    ":kovetel": $("#kovetel_0").val(),
    ":felhasznalo_id": 0
  }
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "data": uj_ertekek,
      "tipus": "ksablonmentes",
    },
    success: function (response) {
      if (response == "nem") {
        $("#alertbox").addClass("alert-danger");
        $("#alertbox").text("Sikertelen rögzítés");
        $("#alertbox").removeClass("hidden");
        $("#bezaras").text("Bezárás");
      } else {
        if (response == "hibasadoszam") {
          $("#alertbox").addClass("alert-danger");
          $("#alertbox").text("Hibás adószám!");
          $("#alertbox").removeClass("hidden");
          $("#bezaras").text("Bezárás");
        } else {
          $("#alertbox").addClass("alert-success");
          $("#alertbox").text("Sikeres rögzítés");
          $("#alertbox").removeClass("hidden");
          $("#alertbox").removeClass("alert-danger");
          $("#bezaras").text("Bezárás");
          setTimeout(() => {
            $("#partner_nev").val("");
            $("#partner_adoszam").val("");
            $("#alertbox").addClass("hidden");
          }, 1000);
          setTimeout(() => {
            window.location.reload();
          }, 1000);
        }
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });

  console.log(uj_ertekek);
}

function editKonyvelesitetelSablon(id) {
  let tmp = id.split("-");
  console.log("művelet:" + tmp);
  if (tmp[0] == "szerkeszt") {
    if ($('#' + id).hasClass("active_button")) {
      const adatok = [];
      adatok.push($('#megnevezes-' + tmp[1]).val());
      adatok.push($('#tartozik-' + tmp[1]).val());
      adatok.push($('#kovetel-' + tmp[1]).val());
      adatok.push($('#old_megnevezes-' + tmp[1]).val());

      $.ajax({
        method: 'post',
        url: "inputkezeles.php",
        data: {
          "ksablon_modositas": adatok
        },
        success: function (response) {
          if (response == "nevMegadasKotelezo") {
            hiba("Megnevezés megadása kötelező!", "alertbox_konyvelesitetel");
          }
          if (response == "hibastartozik") {
            hiba("Hibás Tartozik oldali szám!", "alertbox_konyvelesitetel");
          }
          if (response == "hibaskovetel") {
            hiba("Hibás Követel oldali szám!", "alertbox_konyvelesitetel");
          }
          if (response == "azonosszamok") {
            hiba("A tartozik és követel oldali szám nem lehet egyenlő!", "alertbox_konyvelesitetel");
          }
          if (response == "siker") {
            if ($('#alertbox_konyvelesitetel').hasClass("hidden")) {
              $('#alertbox_konyvelesitetel').removeClass("hidden");
              $('#alertbox_konyvelesitetel').addClass("alert-success");
              $('#alertbox_konyvelesitetel').text("Sikeres rögzítés");
            } else {
              $('#alertbox_konyvelesitetel').removeClass("alert-danger");
              $('#alertbox_konyvelesitetel').addClass("alert-success");
              $('#alertbox_konyvelesitetel').text("Sikeres rögzítés");
            }
            setTimeout(() => {
              $('#alertbox_konyvelesitetel').addClass("hidden");
            }, 1000);
            setTimeout(() => {
              window.location.replace("ksablon.php");
            }, 1000);
          }

        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert("Hiba");
          console.log(textStatus, errorThrown);
        }
      });
    } else {
      $('#megnevezes-' + tmp[1]).removeClass("border-0");
      $('#tartozik-' + tmp[1]).removeClass("border-0");
      $('#kovetel-' + tmp[1]).removeClass("border-0");

      $('#megnevezes-' + tmp[1]).attr("disabled", false);
      $('#tartozik-' + tmp[1]).attr("disabled", false);
      $('#kovetel-' + tmp[1]).attr("disabled", false);

      $('#' + id).addClass("border-info");
      $('#' + id).addClass("active_button");
      $('#' + id).html("");
      $('#' + id).html('<i class="fa-solid fa-floppy-disk"></i>');
    }
  } else {
    if (confirm("Valóban törölni szeretné a " + $('#megnevezes-' + tmp[1]).val() + " elnevezésű könyvelési sablont?") == true) {
      $.ajax({
        method: "post",
        url: "inputkezeles.php",
        data: {
          "ksablon_torles": tmp[1],
        },
        success: function (response) {
          if (response == "sikertelentorles") {
            hiba("A törlés sikertelen!", "alertbox_konyvelesitetel");
          }
          if (response == "sikerestorles") {
            if ($('#alertbox_konyvelesitetel').hasClass("hidden")) {
              $('#alertbox_konyvelesitetel').removeClass("hidden");
              $('#alertbox_konyvelesitetel').addClass("alert-success");
              $('#alertbox_konyvelesitetel').text("Sikeres törlés");
            } else {
              $('#alertbox_konyvelesitetel').removeClass("alert-danger");
              $('#alertbox_konyvelesitetel').addClass("alert-success");
              $('#alertbox_konyvelesitetel').text("Sikeres törlés");
            }
            setTimeout(() => {
              $('#alertbox_konyvelesitetel').addClass("hidden");
            }, 1000);
            setTimeout(() => {
              window.location.replace("ksablon.php");
            }, 1000);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert("Hiba");
          console.log(textStatus, errorThrown);
        },
      });
    }
  }
}

function collectUjFelhasznaloAdatok() {
  let form = $("#felhasznalorogzites_form");
  let forms = [];
  forms[0] = getFormData(form);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "ujfelhasznalodata": forms
    },
    success: function (response) {
      if (response == "nevhiany") {
        hiba("Név megadása kötelező!", "alertbox_felhasznalo");
      }

      if (response == "hianyzojelszo") {
        hiba("Jelszó megadása kötelező!", "alertbox_felhasznalo");
      }

      if (response == "siker") {
        if ($('#alertbox_felhasznalo').hasClass("hidden")) {
          $('#alertbox_felhasznalo').removeClass("hidden");
        } else {
          $('#alertbox_felhasznalo').removeClass("alert-danger");
          $('#alertbox_felhasznalo').addClass("alert-success");
          $('#alertbox_felhasznalo').text("Sikeres rögzítés");
        }
        setTimeout(() => {
          $('#alertbox_felhasznalo').addClass("hidden");
        }, 1000);
        setTimeout(() => {
          window.location.replace("felhasznalok.php");
        }, 1000);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });

}

function collectUjFokonyviszamAdatok() {

  if (!confirm("Biztosan hozzáadja? Később nem lesz lehetőség törlésre!")) {
    return;
  }

  let form = $("#fokonyviszam_rogzites_form");
  let forms = [];
  forms[0] = getFormData(form);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "ujFokonyviszamok": forms
    },
    success: function (response) {
      if (response == "letezoszam") {
        hiba("Ez a főkönyviszám már létezik!", 'alertbox_fokonyviszam');
        setTimeout(() => {
          $('#alertbox_fokonyviszam').addClass("hidden");
        }, 4000);
      }
      if (response == "siker") {
        if ($('#alertbox_fokonyviszam').hasClass("hidden")) {
          $('#alertbox_fokonyviszam').removeClass("hidden");
        } else {
          $('#alertbox_fokonyviszam').removeClass("alert-danger");
          $('#alertbox_fokonyviszam').addClass("alert-success");
          $('#alertbox_fokonyviszam').text("Sikeres rögzítés");
        }
        setTimeout(() => {
          $('#alertbox_fokonyviszam').addClass("hidden");
        }, 1000);
        setTimeout(() => {
          window.location.replace("fokonyviszam_rogzites.php");
        }, 1000);
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });

}

function getMerleg() {
  let form = $("#merleg");
  let forms = [];
  forms[0] = getFormData(form);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "merleg_formdata": forms[0]
    },
    success: function (response) {
      $('#merlegtable').html(response);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });

}

function getEredmenykimutatas() {
  let form = $("#eredmenykimutatas");
  let forms = [];
  forms[0] = getFormData(form);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "eredmenykimutatas_formdata": forms[0]
    },
    success: function (response) {
      $('#eredmenykimutatas_table').html(response);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });

}

function getFokonyviKivonat() {
  let form = $("#fokonyvi_kivonat");
  let forms = [];
  forms[0] = getFormData(form);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "fokonyvi_kivonat": forms[0]
    },
    success: function (response) {
      $('#fokonyvi_kivonat_table').html(response);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });

}

function changeProfilePicture(id) {
  console.log(id);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "profilkep_csere": id
    },
    success: function (response) {
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });
}

function inaktivalas(id) {
  if (!confirm("Biztosan inaktiválni szeretné a felhasználót?")) {
    return;
  }
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "inaktivalas": id
    },
    success: function (response) {
      location.reload();
    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });
}

function addSzamlaszamok() {
  let form = $("#bankszamlarogzites_form");
  let forms = [];
  forms[0] = getFormData(form);
  $.ajax({
    method: 'post',
    url: "inputkezeles.php",
    data: {
      "ujbankszamlaszam": forms
    },
    success: function (response) {
      if (response == "bankszamlahiba") {
        hiba("A beírt karakterek száma nem megfelelő!", "alertbox_bsz");
      }

      if (response == "siker") {
        if ($('#alertbox_bsz').hasClass("hidden")) {
          $('#alertbox_bsz').removeClass("hidden");
        } else {
          $('#alertbox_bsz').removeClass("alert-danger");
          $('#alertbox_bsz').addClass("alert-success");
          $('#alertbox_bsz').text("Sikeres rögzítés");
        }
        setTimeout(() => {
          $('#alertbox_bsz').addClass("hidden");
        }, 1000);
        setTimeout(() => {
          window.location.replace("bankszamlarogz.php");
        }, 1000);
      }

    },
    error: function (jqXHR, textStatus, errorThrown) {
      alert("Hiba");
      console.log(textStatus, errorThrown);
    }
  });

}
let logoutTimer;
const idleTime = 15 * 60 * 1000;

function resetTimer(callback) {
  clearTimeout(logoutTimer);
  logoutTimer = setTimeout(logout, idleTime);

  if (typeof callback === "function") {
    callback(idleTime);
  }
}

let currentInterval;
function displayRemainingTime(timeLeft) {
  clearInterval(currentInterval);
  currentInterval = setInterval(() => {
    const minutes = Math.floor(timeLeft / 60000);
    const seconds = Math.floor((timeLeft % 60000) / 1000);
    if (document.getElementById('timer')) {
      document.getElementById('timer').textContent = `${pad(minutes)}:${pad(seconds)}`;
    }

    if (timeLeft <= 0) {
      clearInterval(currentInterval);
    } else {
      timeLeft -= 1000;
    }
  }, 1000);
}

function pad(number) {
  return number < 10 ? '0' + number : number;
}


function logout() {
  let url = window.location.href.toString().split(window.location.host)[1];
  let str = "list";
  if (url.includes(str)) {
    window.location.href = '../logout.php';
  } else {
    window.location.href = '../php/logout.php';
  }

}

window.onload = () => resetTimer(displayRemainingTime(idleTime));
window.onmousemove = () => resetTimer(displayRemainingTime(idleTime));
window.onmousedown = () => resetTimer(displayRemainingTime(idleTime));
window.onclick = () => resetTimer(displayRemainingTime(idleTime));
window.onscroll = () => resetTimer(displayRemainingTime(idleTime));
window.onkeydown = () => resetTimer(displayRemainingTime(idleTime));
window.onkeyup = () => resetTimer(displayRemainingTime(idleTime));

function showPrintButton() {
  document.getElementById('printButton').style.display = 'inline-block';
}
