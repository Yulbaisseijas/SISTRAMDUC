QUnit.module("Request start of creation process", {
    beforeEach: function () {
        this.plan_creation = $("#plan-septenal-colectivo-creation");
        this.start_creation_btn = this.plan_creation.find("#start-creation-btn");

        this.server = sinon.fakeServer.create();
        this.server.respondImmediately = true;
    },
    afterEach: function() {
        this.server.restore();
    }
});

QUnit.test("creationProcess.start should make an ajax request", function (assert) {
    sinon.stub($, "ajax");

    creationProcess.start();

    var ajax_config = {
        url: routes["start-creation-process"],
        data: creationProcess.getFormData(),
        method: "POST"
    };
    assert.calledWithMatch($.ajax, ajax_config);
    $.ajax.restore();
});

QUnit.test("click on #start-creation-btn should call creationProcess.start", function (assert) {
    var data = creationProcess.getFormData(),
        url = this.plan_creation.find("form").attr("action");

    sinon.stub(creationProcess, "start");
    this.start_creation_btn.trigger("click");

    sinon.assert.called(creationProcess.start);
    creationProcess.start.restore()
});

QUnit.test("click on #start-creation-btn should prevent default action", function (assert) {
    var click_event = jQuery.Event("click");
    click_event.preventDefault = sinon.spy();
    this.start_creation_btn.trigger(click_event);

    sinon.assert.called(click_event.preventDefault);
});

QUnit.test("on successful creationProcess.start a success message should be shown", function (assert) {
    this.server.respondWith([200, {}, ""]);
    sinon.stub(toastr, "success");

    creationProcess.start();

    assert.calledWith(toastr.success, "Proceso iniciado satisfactoriamente.");
    toastr.success.restore();
});

QUnit.test("creationProcess.start must hide the form and make progress html visible", function (assert) {
    this.server.respondWith([200, {}, ""]);
    assert.notOk(this.plan_creation.find("#creation-progress").is(":visible"));

    creationProcess.start();

    assert.notOk(this.plan_creation.find("form").is(":visible"));
    assert.ok(this.plan_creation.find("#creation-progress").is(":visible"));
});

QUnit.test("on successful creationProcess.start, planIndividualDataTable should be loaded", function (assert) {
    sinon.stub(planIndividualDataTable, "load");
    this.server.respondWith([200, {}, ""]);

    creationProcess.start();

    sinon.assert.called(planIndividualDataTable.load);
    planIndividualDataTable.load.restore();
});

QUnit.test("on client error while starting creation process, message sent by server should be displayed", function (assert) {
    var message = "something went wrong";
    this.server.respondWith([400, { "Content-Type": "application/json" }, JSON.stringify(message)]);
    sinon.stub(toastr, "error");

    creationProcess.start({});

    assert.calledWith(toastr.error, message);
    toastr.error.restore();
});

QUnit.test("on server error while starting creation process message should be displayed", function (assert) {
    this.server.respondWith([500, {}, ""]);
    sinon.stub(toastr, "error");

    creationProcess.start();

    assert.calledWith(toastr.error, SERVER_ERROR_MESSAGE);
    toastr.error.restore();
});

QUnit.test("planSeptenalColectivo.get should perform an ajax request", function (assert) {
    sinon.stub($, "ajax");

    planSeptenalColectivo.get(2018);

    var ajaxSettings = {
        url: routes["plan-septenal-colectivo"],
        data: { inicio: 2018 },
        method: "GET"
    };
    assert.calledWithMatch($.ajax, ajaxSettings);
    $.ajax.restore();
});

QUnit.test("initial state before planSeptenalColectivo.get call", function (assert) {
    assert.notOk(this.plan_creation.find("form").is(":visible"));
    assert.notOk(this.plan_creation.find("#creation-progress").is(":visible"));
});

QUnit.test("successful planSeptenalColectivo.get should keep creation form hidden and make creation-progress visible", function (assert) {
    this.server.respondWith([200, { "Content-Type": "application/json" }, JSON.stringify({status: "En creación"})]);

    planSeptenalColectivo.get(2018);

    assert.notOk(this.plan_creation.find("form").is(":visible"), "creation form should be hidden");
    assert.ok(this.plan_creation.find("#creation-progress").is(":visible"), "creation progress should be visible");
});

QUnit.test("successful planSeptenalColectivo.get should load planIndividualDataTable", function (assert) {
    sinon.stub(planIndividualDataTable, "load");
    this.server.respondWith([200, { "Content-Type": "application/json" }, JSON.stringify({status: "En creación"})]);

    planSeptenalColectivo.get(2018);

    var config = { "ajax": routes["get-all-plan-septenal-individual"] + "?inicio=" + creationProcess.next_year };
    assert.calledWith(planIndividualDataTable.load, config);
    planIndividualDataTable.load.restore();
});

QUnit.test("on not found planSeptenalColectivo.get creation form should be visilbe while creation-progress should not", function (assert) {
    this.server.respondWith([404, {}, ""]);

    planSeptenalColectivo.get(2018);

    assert.ok(this.plan_creation.find("form").is(":visible"));
    assert.notOk(this.plan_creation.find("#creation-progress").is(":visible"));
});

QUnit.test("on client error while starting creation process received error message should be displayed", function (assert) {
    var message = "something went wrong";
    this.server.respondWith([400, { "Content-Type": "application/json" }, JSON.stringify(message)]);
    sinon.stub(toastr, "error");

    planSeptenalColectivo.get(2018);

    assert.calledWith(toastr.error, message);
    toastr.error.restore();
});

QUnit.test("on server error while starting creation process message should be displayed", function (assert) {
    this.server.respondWith([500, {}, ""]);
    sinon.stub(toastr, "error");

    planSeptenalColectivo.get(2018);

    assert.calledWith(toastr.error, SERVER_ERROR_MESSAGE);
    toastr.error.restore();
});

QUnit.module("Managing creation process", {
    beforeEach: function () {
        this.plan_creation = $("#plan-septenal-colectivo-creation");
        this.start_creation_btn = this.plan_creation.find("#start-creation-btn");

        this.server = sinon.fakeServer.create();
        this.server.respondImmediately = true;
    },
    afterEach: function() {
        this.server.restore();
    }
});

QUnit.test("planIndividualDataTable.load method should initialize datatable", function (assert) {
    var config = { "ajax": routes["get-all-plan-septenal-individual"] + "?inicio=" + creationProcess.next_year };

    sinon.stub($.fn, "DataTable");

    planIndividualDataTable.load(config);

    assert.calledWith($.fn.DataTable, config);
    $.fn.DataTable.restore();
});

QUnit.module("Managing creation process", {
    beforeEach: function () {
        this.plan_creation = $("#plan-septenal-colectivo-creation");
        this.start_creation_btn = this.plan_creation.find("#start-creation-btn");

        this.server = sinon.fakeServer.create();
        this.server.respondImmediately = true;

        var content = {
            data: [
                [1, "Tony", 5, "Modificando"],
                [2, "Alfred", 3, "Esperando aprobación"],
                [3, "Beth", 8, "Esperando aprobación"]
            ]
        };

        this.server.respondWith([200, {}, JSON.stringify(content)]);
        planIndividualDataTable.load({ ajax: "endpoint"});
    },
    afterEach: function() {
        this.server.restore();
    }
});

QUnit.test("view button must exist per plan inside DataTables", function (assert) {
    assert.equal($(".btn-view-plan").length, 3, "three view buttons must exist");
});

QUnit.test("planIndividualViewer.load method should put the retrieved plan inside the plan widget", function (assert) {
    var sample_state = {
        inicio: 2010,
        tramites: [{ tipo: TRAMITES[0].name, periodo: { start: "01/2010", end: "02/2010" }}]
    };
    this.server.respondWith([200, { "Content-Type": "application/json" }, JSON.stringify(sample_state)]);

    planIndividualViewer.load({ inicio: 2010 });

    assert.deepEqual(planIndividualViewer.planWidget.getState(), sample_state);
});

QUnit.test("View button should request plan septenal individual", function (assert) {
    sinon.stub(planIndividualViewer, "load");

    $(".btn-view-plan").first().trigger("click");
    assert.calledWith(planIndividualViewer.load, {id: 1});

    $(".btn-view-plan").eq(1).trigger("click");
    assert.calledWith(planIndividualViewer.load, {id: 2});

    planIndividualViewer.load.restore();
});

QUnit.test("on successful plan individual view details action a modal should display plan individual", function (assert) {
    planIndividualViewer.load({id: 1})
    sinon.stub(planIndividualViewer, "modal");

    $(".btn-view-plan").first().trigger("click");

    sinon.assert.called(planIndividualViewer.modal);
    planIndividualViewer.modal.restore();
});

QUnit.test("plan individual editing should be disabled", function (assert) {
    assert.notOk(planIndividualViewer.planWidget.grid.enabled, "grid should not be enabled");
});

QUnit.test("approve button must exist per plan that is waiting for approval", function (assert) {
    assert.equal($(".btn-approve-plan").length, 2, "two approve buttons must exist");
});

QUnit.test("click on approve button must make an ajax request", function (assert) {
    sinon.stub($, "ajax");
    this.server.respondWith([200, {}, JSON.stringify({})]);

    $(".btn-approve-plan").eq(0).trigger("click");
    $(".btn-approve-plan").eq(1).trigger("click");

    assert.ok($.ajax.firstCall.calledWithMatch({
        url: routes["approve-plan-septenal-individual"],
        data: { id: 2 }
    }));
    assert.ok($.ajax.secondCall.calledWithMatch({
        url: routes["approve-plan-septenal-individual"],
        data: { id: 3 }
    }));

    $.ajax.restore();
});

QUnit.test("click on approve button must reload the datatable", function (assert) {
    sinon.stub(planIndividualDataTable.dt.ajax, "reload");
    this.server.respondWith([200, {}, JSON.stringify({})]);

    $(".btn-approve-plan").eq(0).trigger("click");
    sinon.assert.called(planIndividualDataTable.dt.ajax.reload);
    planIndividualDataTable.dt.ajax.reload.restore();
});

QUnit.test("after approve request is successfully responded a message should be displayed", function (assert) {
    this.server.respondWith([200, {}, JSON.stringify({})]);
    sinon.stub(toastr, "success");

    $(".btn-approve-plan").eq(0).trigger("click");
    assert.calledWith(toastr.success, "Plan septenal individual aprobado satisfactoriamente.");
    toastr.success.restore();
});

QUnit.test("after approve request is unsuccessfully responded a message should be displayed", function (assert) {
    this.server.respondWith([500, {}, JSON.stringify({})]);
    sinon.stub(toastr, "error");

    $(".btn-approve-plan").eq(0).trigger("click");
    assert.calledWith(toastr.error, SERVER_ERROR_MESSAGE);
    toastr.error.restore();
});
