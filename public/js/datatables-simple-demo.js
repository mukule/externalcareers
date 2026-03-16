window.addEventListener("DOMContentLoaded", (event) => {
  // Simple-DataTables
  // https://github.com/fiduswriter/Simple-DataTables/wiki

  const datatablesSimple = document.getElementById("datatablesSimple");
  if (datatablesSimple) {
    new simpleDatatables.DataTable(datatablesSimple);
  }
});

window.addEventListener("DOMContentLoaded", (event) => {
  // Select all tables with class 'datatable-simple'
  const tables = document.querySelectorAll(".datatable-simple");

  tables.forEach((table) => {
    new simpleDatatables.DataTable(table);
  });
});
