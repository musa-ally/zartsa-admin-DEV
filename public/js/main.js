
var _index = 1;
function searchRole() {
    const query = document.getElementById("role_query").value;
    // console.log(query);
    $.ajax({
        method: 'GET',
        url: search_role_url,
        data: {body: query}
    })
        .done(function (msg) {
            const roles = JSON.parse(JSON.stringify(msg['results']));
            const table = document.getElementById("roleBody");

            console.log(roles)

            $("#roleBody").empty();
            roles.forEach(role => {
                let row = table.insertRow();
                let number = row.insertCell(0);
                number.innerHTML = _index++;
                let display_name = row.insertCell(1);
                display_name.innerHTML = role.display_name;
                let description = row.insertCell(2);
                description.innerHTML = role.description;
                let status = row.insertCell(3);
                if (role.service_status_code == 'AC001'){
                    status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                }else{
                    status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                }
                let actions = row.insertCell(4);
                const actionButtons = '<button type="button" class="btn btn-primary btn-sm px-3"\n' +
                    '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                    '                        <i class="fas fa-eye"></i>\n' +
                    '                    </button>\n' +
                    '                    <button type="button" class="btn btn-warning btn-sm px-3"\n' +
                    '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Edit role">\n' +
                    '                        <i class="fas fa-pen"></i>\n' +
                    '                    </button>\n';

                if (role.service_status_code == 'AC001'){
                    actions.innerHTML = actionButtons+
                    '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                        '                        <i class="fas fa-lock"></i>\n' +
                        '                    </button>';
                }else{
                    actions.innerHTML = actionButtons+
                        '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                        '                        <i class="fas fa-unlock"></i>\n' +
                        '                    </button>';
                }
            })
        })
}

function searchPermission() {
    const query = document.getElementById("permission_query").value;
    // console.log(query);
    $.ajax({
        method: 'GET',
        url: search_permission_url,
        data: {body: query}
    })
        .done(function (msg) {
            const permissions = JSON.parse(JSON.stringify(msg['results']));
            const table = document.getElementById("permissionBody");

            console.log(permissions)

            $("#permissionBody").empty();
            permissions.forEach(permission => {
                let row = table.insertRow();
                let number = row.insertCell(0);
                number.innerHTML = _index++;
                let display_name = row.insertCell(1);
                display_name.innerHTML = permission.display_name;
                let description = row.insertCell(2);
                description.innerHTML = permission.description;
                let status = row.insertCell(3);
                if (permission.service_status_code == 'AC001'){
                    status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                }else{
                    status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                }
                let actions = row.insertCell(4);
                const actionButtons = '<button type="button" class="btn btn-warning btn-sm px-3"\n' +
                    '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Edit role">\n' +
                    '                        <i class="fas fa-pen"></i>\n' +
                    '                    </button>\n';

                if (permission.service_status_code == 'AC001'){
                    actions.innerHTML = actionButtons+
                    '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                        '                        <i class="fas fa-lock"></i>\n' +
                        '                    </button>';
                }else{
                    actions.innerHTML = actionButtons+
                        '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                        '                        <i class="fas fa-unlock"></i>\n' +
                        '                    </button>';;
                }
            })
        });
}

function searchIdTypes() {
    const query = document.getElementById("id_types_query").value;
    // console.log(query);
    $.ajax({
        method: 'GET',
        url: search_id_types_url,
        data: {body: query}
    })
        .done(function (msg) {
            const idTypes = JSON.parse(JSON.stringify(msg['results']));
            const table = document.getElementById("idTypeBody");

            console.log(idTypes)

            $("#idTypeBody").empty();
            idTypes.forEach(idType => {
                let row = table.insertRow();
                let number = row.insertCell(0);
                number.innerHTML = _index++;
                let name = row.insertCell(1);
                name.innerHTML = idType.name;
                let description = row.insertCell(2);
                description.innerHTML = idType.description;
                let status = row.insertCell(3);
                if (idType.service_status_code == 'AC001'){
                    status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                }else{
                    status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                }
                let actions = row.insertCell(4);
                const actionButtons = '<button type="button" class="btn btn-warning btn-sm px-3"\n' +
                    '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                    '                        <i class="fas fa-pen"></i>\n' +
                    '                    </button>\n';

                if (idType.service_status_code == 'AC001'){
                    actions.innerHTML = actionButtons+
                    '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                        '                        <i class="fas fa-lock"></i>\n' +
                        '                    </button>';
                }else{
                    actions.innerHTML = actionButtons+
                        '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                        '                        <i class="fas fa-unlock"></i>\n' +
                        '                    </button>';
                }
            })
        });
}

function searchServices() {
    const query = document.getElementById("service_query").value;
    // console.log(query);
    $.ajax({
        method: 'GET',
        url: search_services_url,
        data: {body: query}
    })
        .done(function (msg) {
            const services = JSON.parse(JSON.stringify(msg['results']));
            const table = document.getElementById("serviceBody");

            console.log(services)

            $("#serviceBody").empty();
            services.forEach(service => {
                let row = table.insertRow();
                let number = row.insertCell(0);
                number.innerHTML = _index++;
                let name = row.insertCell(1);
                name.innerHTML = service.name;
                let description = row.insertCell(2);
                description.innerHTML = service.description;
                let status = row.insertCell(3);
                if (service.service_status_code == 'AC001'){
                    status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                }else{
                    status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                }
                let actions = row.insertCell(4);
                const actionButtons = '<button type="button" class="btn btn-warning btn-sm px-3"\n' +
                    '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                    '                        <i class="fas fa-pen"></i>\n' +
                    '                    </button>\n';

                if (service.service_status_code == 'AC001'){
                    actions.innerHTML = actionButtons+
                    '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                        '                        <i class="fas fa-lock"></i>\n' +
                        '                    </button>';
                }else{
                    actions.innerHTML = actionButtons+
                        '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                        '                        <i class="fas fa-unlock"></i>\n' +
                        '                    </button>';
                }
            })
        });
}

function searchShippingLine() {
    const query = document.getElementById("shipping_line_query").value;
    // console.log(query);
    $.ajax({
        method: 'GET',
        url: search_shipping_lines_url,
        data: {body: query}
    })
        .done(function (msg) {
            const ships = JSON.parse(JSON.stringify(msg['results']));
            const table = document.getElementById("shippingLineBody");

            console.log(ships)

            $("#shippingLineBody").empty();
            ships.forEach(ship => {
                let row = table.insertRow();
                let number = row.insertCell(0);
                number.innerHTML = _index++;
                let name = row.insertCell(1);
                name.innerHTML = ship.name;
                let description = row.insertCell(2);
                description.innerHTML = ship.code;
                let status = row.insertCell(3);
                if (ship.service_status_code == 'AC001'){
                    status.innerHTML = '<span class="badge rounded-pill bg-success text-light">Active</span>';
                }else{
                    status.innerHTML = '<span class="badge rounded-pill bg-danger text-dark">Blocked</span>';
                }
                let actions = row.insertCell(4);
                const actionButtons = '<a href="vessels/'+ship.code+'"><button type="button" class="btn btn-primary btn-sm px-3"\n' +
                    '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View Shipping line">\n' +
                    '                        <i class="fas fa-eye"></i>\n' +
                    '                    </button></a>\n' +
                    '<button type="button" class="btn btn-warning btn-sm px-3"\n' +
                    '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="View role">\n' +
                    '                        <i class="fas fa-pen"></i>\n' +
                    '                    </button>\n';

                if (ship.service_status_code == 'AC001'){
                    actions.innerHTML = actionButtons+
                    '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="Block">\n' +
                        '                        <i class="fas fa-lock"></i>\n' +
                        '                    </button>';
                }else{
                    actions.innerHTML = actionButtons+
                        '<button type="button" class="btn btn-danger btn-sm px-3"\n' +
                        '                            data-mdb-toggle="tooltip" data-mdb-placement="bottom" title="UnBlock">\n' +
                        '                        <i class="fas fa-unlock"></i>\n' +
                        '                    </button>';;
                }
            })
        });
}



function addRemovePermission(permission_id) {
    const permissionElement = document.getElementById(permission_id);
    var action = 0
    if (permissionElement.checked){
        action = 1
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        method: 'POST',
        url: permission_url,
        data: {'action': action, 'role_id': roleId, 'permission_id': permission_id}
    }).done(function (msg) {
        const result = JSON.parse(JSON.stringify(msg['results']))
        if(result.status_code !== 300){
            permissionElement.checked = !permissionElement.checked;
        }
    })
}

function addRemovePermissionToUser(permission_id) {
    const permissionElement = document.getElementById(permission_id);
    let action = 0
    if (permissionElement.checked){
        action = 1
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        method: 'POST',
        url: user_permission_url,
        data: {'action': action, 'permission_id': permission_id, 'user_id': user_id}
    }).done(function (msg) {
        const result = JSON.parse(JSON.stringify(msg['results']))
        console.log(result)
        if(result.status_code !== 300){
            permissionElement.checked = !permissionElement.checked;

        }
    })
}

function addRemovePasswordPolicy(policy_name) {
    const policyElement = document.getElementById(policy_name);
    var action = 0
    if (policyElement.checked){
        action = 1
    }
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        method: 'POST',
        url: policy_url,
        data: {'action': action, 'policy_name': policy_name}
    }).done(function (msg) {
        const result = JSON.parse(JSON.stringify(msg['results']))
        console.log(result)
        if(result.status_code !== 300){
            policyElement.checked = !policyElement.checked;
        }
    })
}

function switchCurrency(id) {
    const currency = document.getElementById(id);
    internationalNumberFormat = new Intl.NumberFormat('en-US')
    if (currency.checked){
        document.getElementById('switchCurrency').innerHTML = 'Switch to TZS'

        document.getElementById('dailyRevenueHeader').innerHTML = 'USD '+internationalNumberFormat.format(daily_usd)
        document.getElementById('weeklyRevenueHeader').innerHTML = 'USD '+internationalNumberFormat.format(weekly_usd)
        document.getElementById('monthlyRevenueHeader').innerHTML = 'USD '+internationalNumberFormat.format(monthly_usd)
        document.getElementById('yearlyRevenueHeader').innerHTML = 'USD '+internationalNumberFormat.format(yearly_usd)

        // document.getElementById('tblColDaily').innerHTML = 'Daily income (USD)'
        // document.getElementById('tblColWeekly').innerHTML = 'Weekly income (USD)'
        // document.getElementById('tblColMonthly').innerHTML = 'Monthly income (USD)'
        // document.getElementById('tblColYearly').innerHTML = 'Yearly income (USD)'
    }else{
        document.getElementById('switchCurrency').innerHTML = 'Switch to USD'

        document.getElementById('dailyRevenueHeader').innerHTML = 'TZS '+internationalNumberFormat.format(daily_tzs)
        document.getElementById('weeklyRevenueHeader').innerHTML = 'TZS '+internationalNumberFormat.format(weekly_tzs)
        document.getElementById('monthlyRevenueHeader').innerHTML = 'TZS '+internationalNumberFormat.format(monthly_tzs)
        document.getElementById('yearlyRevenueHeader').innerHTML = 'TZS '+internationalNumberFormat.format(yearly_tzs)

        // document.getElementById('tblColDaily').innerHTML = 'Daily income (TZS)'
        // document.getElementById('tblColWeekly').innerHTML = 'Weekly income (TZS)'
        // document.getElementById('tblColMonthly').innerHTML = 'Monthly income (TZS)'
        // document.getElementById('tblColYearly').innerHTML = 'Yearly income (TZS)'
    }
}

function getPermissions(){
    const selected_role = document.getElementById("role").value;
    $.ajax({
        method: 'GET',
        url: permissions_url,
        data: {body: selected_role}
    })
        .done(function (msg) {
            console.log(msg['permissions']);
            const obj = JSON.parse(JSON.stringify(msg['permissions']));

            for (let i = 0; i < obj.length; i++) {
                const object = obj[i];
            }
        });
}

$('#serviceBlockModal').on('show.bs.modal', function (e) {

    //get data-id attribute of the clicked element
    var rowId = $(e.relatedTarget).data('row-id');
    var serviceAction = $(e.relatedTarget).data('service-action');

    //populate the textbox
    $(e.currentTarget).find('h5[id="serviceBlockModal"]').text(serviceAction);
    $(e.currentTarget).find('input[name="row_id"]').val(rowId);
});

$('#roleBlockModal').on('show.bs.modal', function (e) {

    //get data-id attribute of the clicked element
    var rowId = $(e.relatedTarget).data('row-id');
    var serviceAction = $(e.relatedTarget).data('service-action');

    //populate the textbox
    $(e.currentTarget).find('h5[id="roleBlockModal"]').text(serviceAction);
    $(e.currentTarget).find('input[name="row_id"]').val(rowId);
});

$('#permissionBlockModal').on('show.bs.modal', function (e) {

    //get data-id attribute of the clicked element
    var rowId = $(e.relatedTarget).data('row-id');
    var serviceAction = $(e.relatedTarget).data('service-action');

    //populate the textbox
    $(e.currentTarget).find('h5[id="permissionBlockModal"]').text(serviceAction);
    $(e.currentTarget).find('input[name="row_id"]').val(rowId);
});

$('#idBlockModal').on('show.bs.modal', function (e) {

    //get data-id attribute of the clicked element
    var rowId = $(e.relatedTarget).data('row-id');
    var serviceAction = $(e.relatedTarget).data('service-action');

    //populate the textbox
    $(e.currentTarget).find('h5[id="idBlockModal"]').text(serviceAction);
    $(e.currentTarget).find('input[name="row_id"]').val(rowId);
});

$('#serviceEditModal').on('show.bs.modal', function (e) {

    //get data-id attribute of the clicked element
    const serviceId = $(e.relatedTarget).data('service-id');
    const serviceName = $(e.relatedTarget).data('service-name');
    const serviceDesc = $(e.relatedTarget).data('service-desc');
    const serviceIcon = $(e.relatedTarget).data('service-icon');

    //populate the textbox
    $(e.currentTarget).find('input[name="service_id"]').val(serviceId);
    $(e.currentTarget).find('input[name="name"]').val(serviceName);
    $(e.currentTarget).find('textarea[name="description"]').val(serviceDesc);

    console.log(serviceIcon)
    let imageName = '/images/services/boat-with-containers.png';
    if (serviceIcon != null){
        imageName = '/images/services/'+serviceIcon;
    }
    document.getElementById('image-div').innerHTML = '<img src="'+imageName+'" alt="service image" width="50"> &nbsp;' +
        ' <select name="icon" is="ms-dropdown">\n' +
        '    <option value="" data-image="">Choose icon...</option>\n' +
        '    <option value="cargo.svg" data-image="/images/services/cargo.svg">Cargo</option>\n' +
        '    <option value="forklift.svg" data-image="/images/services/forklift.svg">Fork lift</option>\n' +
        '    <option value="warehouse.svg" data-image="/images/services/warehouse.svg">Ware house</option>\n' +
        '  </select>';
});

$('#manifestModal1').on('show.bs.modal', function (e) {
    //get data-id attribute of the clicked element
    const vesselName = $(e.relatedTarget).data('vessel-name');
    $(e.currentTarget).find('input[name="vessel_name"]').val(vesselName);
});

$('#editRoleModal1').on('show.bs.modal', function (e) {
    //get data-id attribute of the clicked element
    const roleId = $(e.relatedTarget).data('role-id');
    const roleName = $(e.relatedTarget).data('role-name');
    const roleDescription = $(e.relatedTarget).data('role-description');

    $(e.currentTarget).find('input[name="role_id"]').val(roleId);
    $(e.currentTarget).find('input[name="name"]').val(roleName);
    $(e.currentTarget).find('textarea[name="description"]').val(roleDescription);
});

$('#editPermissionModal1').on('show.bs.modal', function (e) {
    //get data-id attribute of the clicked element
    const permissionId = $(e.relatedTarget).data('permission-id');
    const permissionName = $(e.relatedTarget).data('permission-name');
    const permissionDescription = $(e.relatedTarget).data('permission-description');

    $(e.currentTarget).find('input[name="permission_id"]').val(permissionId);
    $(e.currentTarget).find('input[name="name"]').val(permissionName);
    $(e.currentTarget).find('textarea[name="description"]').val(permissionDescription);
});

$('#editIdModal1').on('show.bs.modal', function (e) {
    //get data-id attribute of the clicked element
    const id = $(e.relatedTarget).data('identification-id');
    const idName = $(e.relatedTarget).data('id-name');
    const idDescription = $(e.relatedTarget).data('id-description');

    $(e.currentTarget).find('input[name="identification_id"]').val(id);
    $(e.currentTarget).find('input[name="name"]').val(idName);
    $(e.currentTarget).find('textarea[name="description"]').val(idDescription);
});
