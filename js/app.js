const $ = (id) => document.getElementById(id);
const api = "css/php/";
let allMentors = [];

const fields = {
    add: ["mentor-name", "employee-id", "department", "max-mentees"],
    edit: ["edit-mentor-name", "edit-employee-id", "edit-department", "edit-max-mentees"]
};

function error(id, message = "") {
    $(id).classList.toggle("input-error", Boolean(message));
    $(`${id}-error`).textContent = message;
}

function validForm(prefix, photoRequired) {
    let valid = true;
    const [name, employee, department, max] = fields[prefix].map($);

    [[name, "Mentor name cannot be empty."], [employee, "Employee ID cannot be empty."],
    ].forEach(([input, message]) => {
        const invalid = !input.value.trim();
        error(input.id, invalid ? message : "");
        valid &&= !invalid;
    });

    const invalidDepartment = !department.value;
    error(department.id, invalidDepartment ? "Please select a department." : "");
    valid &&= !invalidDepartment;

    const invalidMax = !Number.isInteger(Number(max.value)) || Number(max.value) <= 0;
    error(max.id, invalidMax ? "Maximum mentees must be a positive whole number." : "");
    valid &&= !invalidMax;

    const photo = $(prefix === "add" ? "profile-photo" : "edit-profile-photo");
    const hasPhoto = photo.files.length > 0;
    const invalidPhoto = (photoRequired && !hasPhoto) || (hasPhoto && !isImage(photo.files[0]));
    error(photo.id, invalidPhoto ? "Only JPG and PNG images are allowed." : "");
    return valid && !invalidPhoto;
}

function isImage(file) {
    return ["image/jpeg", "image/png"].includes(file.type);
}

function formData(prefix, id = null) {
    const data = new FormData();
    const values = fields[prefix].map($);
    if (id !== null) data.append("id", id);
    ["name", "employee_id", "department", "max_mentees"]
        .forEach((key, index) => data.append(key, values[index].value.trim()));
    const photo = $(prefix === "add" ? "profile-photo" : "edit-profile-photo");
    if (photo.files[0]) data.append("profile_photo", photo.files[0]);
    return data;
}

async function send(file, data) {
    const response = await fetch(api + file, { method: "POST", body: data });
    return response.json();
}

function message(text) {
    const box = $("success-message");
    box.textContent = text;
    box.hidden = false;
    setTimeout(() => box.hidden = true, 3000);
}

async function loadMentors() {
    try {
        const data = await (await fetch(api + "get_mentors.php")).json();
        if (!data.success) throw new Error(data.message);
        allMentors = data.mentors;
        filterMentors();
    } catch (err) {
        console.error("Failed to load mentors:", err);
    }
}

function filterMentors() {
    const department = $("department-filter").value;
    render(department ? allMentors.filter((mentor) => mentor.department === department) : allMentors);
}

function render(mentors) {
    const body = $("mentor-table-body");
    body.replaceChildren();
    $("empty-table-message").hidden = mentors.length > 0;

    mentors.forEach((mentor) => {
        const row = document.createElement("tr");
        [mentor.name, mentor.employee_id, mentor.department, mentor.max_mentees].forEach((value) => {
            const cell = document.createElement("td");
            cell.textContent = value;
            row.appendChild(cell);
        });

        const photoCell = document.createElement("td");
        const photo = document.createElement("img");
        photo.src = mentor.photo_path;
        photo.alt = "Mentor Photo";
        photo.className = "profile-photo";
        photoCell.appendChild(photo);
        row.appendChild(photoCell);

        const actions = document.createElement("td");
        actions.className = "actions";
        actions.append(button("Edit", "btn-edit", () => openEdit(mentor)),
            button("Delete", "btn-delete", () => openDelete(mentor.id)));
        row.appendChild(actions);
        body.appendChild(row);
    });
}

function button(text, className, handler) {
    const button = document.createElement("button");
    button.type = "button";
    button.textContent = text;
    button.className = `btn ${className}`;
    button.addEventListener("click", handler);
    return button;
}

let editingId = null;
let deletingId = null;

function openEdit(mentor) {
    editingId = mentor.id;
    ["name", "employee_id", "department", "max_mentees"]
        .forEach((key, index) => $(fields.edit[index]).value = mentor[key]);
    $("edit-profile-photo").value = "";
    $("edit-mentor-popup").hidden = false;
    validateEdit();
}

function openDelete(id) {
    deletingId = id;
    $("delete-confirmation").hidden = false;
}

function validateAdd() {
    $("submit-button").disabled = !validForm("add", true);
}

function validateEdit() {
    $("save-edit-button").disabled = !validForm("edit", false);
}

fields.add.forEach((id) => $(id).addEventListener("input", validateAdd));
$("department").addEventListener("change", validateAdd);
$("profile-photo").addEventListener("change", validateAdd);
$("department-filter").addEventListener("change", filterMentors);
fields.edit.forEach((id) => $(id).addEventListener("input", validateEdit));
$("edit-department").addEventListener("change", validateEdit);
$("edit-profile-photo").addEventListener("change", validateEdit);

$("mentor-form").addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!validForm("add", true)) return;
    try {
        const data = await send("add_mentor.php", formData("add"));
        if (!data.success) return message(data.message);
        event.target.reset();
        $("submit-button").disabled = true;
        await loadMentors();
        message("Mentor added successfully.");
    } catch (err) { message("Server error. Please try again."); console.error(err); }
});

$("edit-form").addEventListener("submit", async (event) => {
    event.preventDefault();
    if (!validForm("edit", false)) return;
    try {
        const data = await send("update_mentor.php", formData("edit", editingId));
        if (!data.success) return message(data.message);
        $("edit-mentor-popup").hidden = true;
        await loadMentors();
        message("Mentor updated successfully.");
    } catch (err) { message("Server error. Please try again."); console.error(err); }
});

$("cancel-edit-button").addEventListener("click", () => $("edit-mentor-popup").hidden = true);
$("cancel-delete-button").addEventListener("click", () => $("delete-confirmation").hidden = true);
$("confirm-delete-button").addEventListener("click", async () => {
    if (deletingId === null) return;
    try {
        const data = await send("delete_mentor.php", new URLSearchParams({ id: deletingId }));
        if (!data.success) return message(data.message);
        $("delete-confirmation").hidden = true;
        await loadMentors();
        message("Mentor deleted successfully.");
    } catch (err) { message("Server error. Please try again."); console.error(err); }
});

loadMentors();