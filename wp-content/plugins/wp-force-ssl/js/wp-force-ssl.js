/**
 * WP Force SSL
 * https://wpforcessl.com/
 * (c) WebFactory Ltd, 2017-2021
 */

jQuery(document).ready(function ($) {
  /** Tabs loading and changing **/
  if (wp_force_ssl.is_plugin_page) {
    $("#wfssl-tabs")
      .tabs({
        create: function () {
          if (window.location.hash) {
            $("#wfssl-tabs").tabs(
              "option",
              "active",
              $('a[href="' + location.hash + '"]')
                .parent()
                .index()
            );
          }
        },
        activate: function (event, ui) {
          localStorage.setItem("wfssl-tabs", $("#wfssl-tabs").tabs("option", "active"));
        },
        active: localStorage.getItem("wfssl-tabs") || 0,
      })
      .show();
  }

  $(window).on("hashchange", function () {
    $("#wfssl-tabs").tabs(
      "option",
      "active",
      $('a[href="' + location.hash + '"]')
        .parent()
        .index()
    );
  });

  // helper for switching tabs & linking anchors in different tabs
  $(".settings_page_wp-force-ssl").on("click", ".change-tab", function (e) {
    e.preventDefault();
    $("#wfssl-tabs").tabs("option", "active", $(this).data("tab"));

    // get the link anchor and scroll to it
    target = this.href.split("#")[1];
    if (target) {
      $.scrollTo("#" + target, 500, { offset: { top: -50, left: 0 } });
    }

    $(this).blur();
    return false;
  }); // jump to tab/anchor helper

  // helper for scrolling to anchor
  $(".settings_page_wp-force-ssl").on("click", ".scrollto", function (e) {
    e.preventDefault();

    // get the link anchor and scroll to it
    target = this.href.split("#")[1];
    if (target) {
      $.scrollTo("#" + target, 500, { offset: { top: -50, left: 0 } });
    }

    $(this).blur();
    return false;
  }); // scroll to anchor helper

  /* Status Tab */

  // load test results
  load_test_results(false);

  $(".settings_page_wp-force-ssl").on("click", ".run-tests", function () {
    load_test_results(true);
  });

  function load_test_results(force) {
    $("#status_progress_wrapper").hide();
    $(".run-tests").hide();
    $("#status_tasks").hide();
    $("#test-results-wrapper").html('<div class="loading-wrapper"><img class="wfssl_flicker" src="' + wp_force_ssl.icon_url + '" alt="Loading. Please wait." title="Loading. Please wait."><p>Loading. Please wait.</p></div>');

    $.ajax({
      url: ajaxurl,
      data: {
        action: "wp_force_ssl_run_tool",
        _ajax_nonce: wp_force_ssl.nonce_run_tool,
        tool: "tests_results",
        force: force,
      },
    })
      .done(function (data) {
        if (data.success) {
          tests_total = 0;
          tests_passed = 0;
          tests_results = data.data;
          tests_results_html = '<table class="form-table">';
          for (test in tests_results) {
            tests_total++;

            tests_results_html += '<tr data-status="' + tests_results[test].status + '"><td>';

            switch (tests_results[test].status) {
              case "fail":
                tests_results_html += '<div class="wfssl-badge wfssl-badge-red tooltip" title="Test failed">failed</div>';
                break;
              case "warning":
                tests_results_html += '<div class="wfssl-badge wfssl-badge-yellow tooltip" title="Test warning">warning</div>';
                break;
              case "pass":
                tests_results_html += '<div class="wfssl-badge wfssl-badge-green tooltip" title="Test passed">passed</div>';
                tests_passed++;
                break;
            }
            tests_results_html += "</td>";
            tests_results_html += "<td>" + tests_results[test].title + "<br /><small>" + tests_results[test].description + "</small></td>";
            tests_results_html += "</tr>";
          }

          tests_results_html += "</table>";
          var progress = Math.floor((tests_passed / tests_total) * 100);
          $("#status_progress").css("width", progress + "%");
          $("#status_progress_text").html(progress + "%");
          $("#wfssl-failed-tests").html(tests_total - tests_passed);
          $("#status_progress_wrapper").show();
          $("#status_tasks").html('<div class="status-tasks status-tasks-selected" data-tasks="all">All tasks (' + tests_total + ')</div><div class="status-tasks" data-tasks="remaining">Remaining tasks (' + (tests_total - tests_passed) + ")</div>");
          $("#status_tasks").show();
          $("#test-results-wrapper").html(tests_results_html);
          $(".run-tests").show();

          refresh_tooltips();
        } else {
          wfssl_swal.fire({
            type: "error",
            title: wp_force_ssl.undocumented_error,
          });
        }
      })
      .fail(function (data) {
        wfssl_swal.fire({
          type: "error",
          title: wp_force_ssl.undocumented_error,
        });
      });
  }

  $(".settings_page_wp-force-ssl").on("click", ".status-tasks", function (e) {
    $(".status-tasks").removeClass("status-tasks-selected");
    $(this).addClass("status-tasks-selected");
    var test_status = $(this).data("tasks");
    if (test_status == "all") {
      $('[data-status="pass"]').show();
    } else {
      $('[data-status="pass"]').hide();
    }
  });

  /* Settings Tab */
  $(".settings_page_wp-force-ssl").on("click", ".save-ssl-options", function (e) {
    e.preventDefault();
    save_ssl_options();
  });

  function save_ssl_options() {
    block_ui();
    $.get({
      url: ajaxurl,
      data: {
        action: "wp_force_ssl_run_tool",
        _ajax_nonce: wp_force_ssl.nonce_run_tool,
        tool: "save_ssl_options",
        extra_data: {
          fix_frontend_mixed_content_fixer: Number($("#fix_frontend_mixed_content_fixer").is(":checked")),
          fix_backend_mixed_content_fixer: Number($("#fix_backend_mixed_content_fixer").is(":checked")),
          hsts: Number($("#hsts").is(":checked")),
          force_secure_cookies: Number($("#force_secure_cookies").is(":checked")),
          htaccess_301_redirect: Number($("#htaccess_301_redirect").is(":checked")),
          php_301_redirect: Number($("#php_301_redirect").is(":checked")),
          xss_protection: Number($("#xss_protection").is(":checked")),
          x_content_options: Number($("#x_content_options").is(":checked")),
          referrer_policy: Number($("#referrer_policy").is(":checked")),
          expect_ct: Number($("#expect_ct").is(":checked")),
          x_frame_options: Number($("#x_frame_options").is(":checked")),
          adminbar_menu: Number($("#adminbar_menu").is(":checked")),
          permissions_policy: Number($("#permissions_policy").is(":checked")),
          permissions_policy_rules: $("#permissions_policy_rules").val(),
          cert_expiration_email: $("#cert_expiration_email").val(),
        },
      },
    })
      .always(function (data) {
        wfssl_swal.close();
      })
      .done(function (data) {
        if (data.success) {
          old_settings = $("#tab_settings *").not(".skip-save").serialize();
          wfssl_swal
            .fire({
              type: "success",
              title: "Options saved",
              timer: 1500,
              showConfirmButton: false,
            })
            .then((result) => {
              window.location.href = wp_force_ssl.settings_url;
            });
        } else {
          wfssl_swal.fire({
            type: "error",
            title: wp_force_ssl.documented_error + " " + data.data,
          });
        }
      })
      .fail(function (data) {
        wfssl_swal.fire({ type: "error", title: wp_force_ssl.undocumented_error });
      });

    return false;
  }

  // handle permission policy popup
  $("#permissions_policy").on("change", function () {
    if ($("#permissions_policy").is(":checked")) {
      $("#configure_permissions_policy").show();
    } else {
      $("#configure_permissions_policy").hide();
    }
  });

  $("#configure_permissions_policy").on("click", function () {
    var features = ["accelerometer", "autoplay", "camera", "encrypted-media", "fullscreen", "geolocation", "gyroscope", "magnetometer", "microphone", "midi", "payment", "picture-in-picture", "sync-xhr", "usb", "interest-cohort"];
    var permissions_policy_rules = JSON.parse($("#permissions_policy_rules").val());

    var html = "<table>";
    html += "<tr>";
    html += "<th>Feature</th>";
    html += '<th>allowed <span style="line-height: 25px;" class="dashicons dashicons-editor-help tooltip" title="No restrictions for this feature."></span></th>';
    html += '<th>self    <span style="line-height: 25px;" class="dashicons dashicons-editor-help tooltip" title="This is feature is only allowed for content on your own domain. Embedded content won\'t be able to use it."></span></th>';
    html += '<th>none    <span style="line-height: 25px;" class="dashicons dashicons-editor-help tooltip" title="Disable this feature completely."></span></th>';
    html += "</tr>";
    for (f in features) {
      if (!permissions_policy_rules.hasOwnProperty(features[f])) {
        permissions_policy_rules[features[f]] = "allowed";
      }

      html += "<tr>";
      html += "<td>" + features[f] + "</td>";
      html += '<td><input type="radio" name="' + features[f] + '" value="allowed" ' + (permissions_policy_rules[features[f]] == "allowed" ? "checked" : "") + "/></td>";
      html += '<td><input type="radio" name="' + features[f] + '" value="self" ' + (permissions_policy_rules[features[f]] == "self" ? "checked" : "") + " /></td>";
      html += '<td><input type="radio" name="' + features[f] + '" value="none" ' + (permissions_policy_rules[features[f]] == "none" ? "checked" : "") + " /></td>";
      html += "</tr>";
    }
    html += "</table>";
    wfssl_swal
      .fire({
        html: html,
        width: 500,
        height: 400,
        allowEnterKey: true,
        showCancelButton: true,
        showCloseButton: true,
        allowEscapeKey: true,
        allowOutsideClick: false,
        confirmButtonText: "Save permission policy",
        cancelButtonText: "Cancel",
        onRender: function () {
          refresh_tooltips();
        },
      })
      .then((result) => {
        if (result.value) {
          tmp = wfssl_swal.getContent();
          $(tmp)
            .find('input[type="radio"]:checked')
            .each(function () {
              permissions_policy_rules[$(this).attr("name")] = $(this).val();
            });

          $("#permissions_policy_rules").val(JSON.stringify(permissions_policy_rules));
        } else {
          wfssl_swal.close();
        }
      });
  });

  var old_settings = $("#tab_settings *").not(".skip-save").serialize();

  $(window).on("beforeunload", function (e) {
    if (wp_force_ssl.is_activated && $("#tab_settings *").not(".skip-save").serialize() != old_settings) {
      msg = "There are unsaved changes that will not be saved if you leave the page. Please save changes first.\nContinue?";
      e.returnValue = msg;
      return msg;
    }
  });

  /* Content Scanner Tab */

  var scanner_queue = [];
  var total_pages = 0;
  var analysis_table = false;

  function scanner_refresh_results() {
    $.ajax({
      url: ajaxurl,
      data: {
        action: "wp_force_ssl_run_tool",
        _ajax_nonce: wp_force_ssl.nonce_run_tool,
        tool: "scanner_results",
      },
    })
      .done(function (data) {
        if (data.data.last_scan != false) {
          $(".scanner-stats").html("The last scan was performed " + data.data.last_scan + " ago, total pages scanned <span>" + data.data.total_pages + "</span>");
        } else {
          $(".scanner-stats").html("You have not scanned your website yet. Run your first scan now.");
        }
      })
      .fail(function (data) {
        wfssl_swal.fire({
          type: "error",
          title: wp_force_ssl.undocumented_error,
        });
      });
    scanner_refresh_results_table();
  }

  function scanner_refresh_results_table() {
    if (analysis_table != false) {
      analysis_table.destroy();
      analysis_table = false;
    }

    analysis_table = $("#scanner-results").DataTable({
      sAjaxSource: ajaxurl + "?action=wp_force_ssl_run_tool&_ajax_nonce=" + wp_force_ssl.nonce_run_tool + "&tool=scanner_results_dt",
      bProcessing: true,
      bServerSide: true,
      bLengthChange: 1,
      bProcessing: true,
      bStateSave: 0,
      bAutoWidth: 0,
      columnDefs: [
        {
          targets: [0],
          className: "dt-body-left",
          width: 100,
        },
        {
          targets: [1, 2, 3],
          className: "dt-body-left dt-head-center",
        },
      ],
      fixedColumns: true,
      drawCallback: function () {
        refresh_tooltips();
      },
      initComplete: function () {
        refresh_tooltips();
      },
      language: {
        loadingRecords: "&nbsp;",
        processing: '<div class="wfssl-flicker"><img width="64" src="' + wp_force_ssl.icon_url + '" /></div>',
        emptyTable: "<div id='scanner-results-none'><span class='dashicons dashicons-archive'></span><br />No mixed content has been found yet!</div>",
        searchPlaceholder: "Type something to search ...",
        search: "",
      },
      order: [[0, "desc"]],
      iDisplayLength: 25,
      sPaginationType: "full_numbers",
      dom: '<"settings_page_301redirects_top">rt<"bottom"lp><"clear">',
    });

    $("#start-scanner").show();
  }

  $.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
    wfssl_swal.fire({
      type: "error",
      text: message,
    });
  };

  $("#start-scanner").on("click", function () {
    $.ajax({
      url: ajaxurl,
      data: {
        action: "wp_force_ssl_run_tool",
        _ajax_nonce: wp_force_ssl.nonce_run_tool,
        tool: "scanner_start",
      },
    })
      .done(function (data) {
        $("#scanner_progress").css("width", "0%");
        $("#scanner_progress_wrapper").show();
        for (page in wp_force_ssl.scanner_pages) {
          scanner_queue.push(wp_force_ssl.scanner_pages[page].page_id);
          total_pages++;
        }
        $(".scanner-stats").html("Scanner starting ...");
        scanner_do_queue();
      })
      .fail(function (data) {
        wfssl_swal.fire({
          type: "error",
          title: wp_force_ssl.undocumented_error,
        });
      });
  });

  scanner_refresh_results();

  function scanner_do_queue() {
    if (scanner_queue.length) {
      var current_page = scanner_queue.shift();
      $.ajax({
        url: ajaxurl,
        data: {
          action: "wp_force_ssl_run_tool",
          _ajax_nonce: wp_force_ssl.nonce_run_tool,
          tool: "scanner_page",
          page: current_page,
        },
      })
        .always(function (data) {
          scanner_parse_results(data.data);
        })
        .done(function (data) {
          if (data.success) {
          } else {
            wfssl_swal.fire({
              type: "error",
              title: wp_force_ssl.undocumented_error,
            });
          }
        })
        .fail(function (data) {
          wfssl_swal.fire({
            type: "error",
            title: wp_force_ssl.undocumented_error,
          });
        })
        .then(scanner_do_queue);
    }
  }

  function scanner_parse_results(data) {
    var progress = Math.floor(((total_pages - scanner_queue.length) / total_pages) * 100);
    $("#scanner_progress").css("width", progress + "%");
    $("#scanner_progress_text").html(progress + "%");
    $(".scanner-stats").html("Total pages scanned <span>" + (total_pages - scanner_queue.length) + "</span>");
    if (scanner_queue.length == 0) {
      $("#scanner_progress_wrapper").hide();
      scanner_refresh_results();
    }
  }

  /* SSL Certificate Tab */
  var generate_certificate_running = true;
  generate_ssl_certificate(false, true);

  $(".settings_page_wp-force-ssl").on("click", ".generate-ssl-certificate", function () {
    generate_certificate_running = true;
    generate_ssl_certificate(false);
    $(".generate-ssl-certificate").html("Next");
  });

  $(".settings_page_wp-force-ssl").on("click", ".generate-ssl-certificate-reset", function () {
    $("#generate_ssl_certificate_loader").show();
    generate_ssl_certificate(true);
    $(".generate-ssl-certificate").html("Generate SSL Certificate");
  });

  function generate_ssl_certificate(reset, return_status = false) {
    console.log("Generate cert");
    if (reset === true) {
      generate_certificate_running = true;
    } else {
      reset = false;
    }

    if (generate_certificate_running == false) {
      $("#generate_ssl_certificate_loader").hide();
      return false;
    } else {
      $("#generate_ssl_certificate_loader").show();
    }

    var form_data = false;
    if (document.getElementById("generate_ssl_certificate_html")) {
      form_data = new FormData(document.getElementById("generate_ssl_certificate_html"));
    }
    var object = {};
    if (form_data !== false) {
      form_data.forEach((value, key) => (object[key] = value));
    }

    $.ajax({
      url: ajaxurl,
      method: "POST",
      data: {
        action: "wp_force_ssl_run_tool",
        _ajax_nonce: wp_force_ssl.nonce_run_tool,
        tool: "generate_certificate",
        reset: reset,
        return_status: return_status,
        form: object,
      },
    })
      .always(function (data) {})
      .done(function (data) {
        if (data.success) {
          if (data.data.step > 1) {
            $('.generate-ssl-certificate-reset').show();
            $('.generate-ssl-certificate-toggle-log').show();
            $('.generate-ssl-certificate').html('Next');
          } else {
            $('.generate-ssl-certificate-reset').hide();
            $('.generate-ssl-certificate-toggle-log').hide();
            $('.generate-ssl-certificate').html('Generate SSL Certificate');
          }
          if (data.data.error || data.data.step > 3) {
            $(".generate-ssl-certificate").hide();
          } else {
            $(".generate-ssl-certificate").show();
          }
          if (data.data.continue) {
            generate_certificate_running = true;
          } else {
            generate_certificate_running = false;
          }
          $("#generate_ssl_certificate_html").html(data.data.html);
          var log_html = "";
          if (data.data.log) {
            for (l in data.data.log) {
              log_html += '<div class="generate_ssl_certificate_log_entry ' + (data.data.log[l].error ? "error" : "") + '"><strong>' + data.data.log[l].time + "</strong> " + data.data.log[l].message + "</div>";
            }
          }
          $("#generate_ssl_certificate_log").html(log_html);
        } else {
          wfssl_swal.fire({
            type: "error",
            title: wp_force_ssl.undocumented_error,
          });
        }
      })
      .fail(function (data) {
        wfssl_swal.fire({
          type: "error",
          title: wp_force_ssl.undocumented_error,
        });
      })
      .then(generate_ssl_certificate);
  }

  $(".generate-ssl-certificate-toggle-log").click(function () {
    $(this).hide();
    $("#generate_ssl_certificate_log").show();
  });

  $("#generate_ssl_certificate_html").on("click", ".generate-ssl-certificate-view", function () {
    $.ajax({
      url: ajaxurl,
      data: {
        action: "wp_force_ssl_run_tool",
        _ajax_nonce: wp_force_ssl.nonce_run_tool,
        tool: "generate_certificate_details",
      },
    })
      .always(function (data) {})
      .done(function (data) {
        wfssl_swal.fire({
          html: data.data,
          width: 800,
          height: 600,
          allowEnterKey: true,
          showCancelButton: false,
          showCloseButton: true,
          allowEscapeKey: true,
          allowOutsideClick: false,
          confirmButtonText: "OK",
          cancelButtonText: "Cancel",
          onRender: function () {
            refresh_tooltips();
          },
        });
      })
      .fail(function (data) {
        wfssl_swal.fire({
          type: "error",
          title: wp_force_ssl.undocumented_error,
        });
      });
  });

  // load SSL Certificate info
  load_ssl_cert_info();

  $(".settings_page_wp-force-ssl").on("click", ".refresh-certificate-info", function () {
    $("#ssl_cert_details").html('Loading certificate information ... <span class="wfssl-green wfssl_rotating dashicons dashicons-update"></span>');
    load_ssl_cert_info(true);
  });

  function load_ssl_cert_info(force) {
    $.ajax({
      url: ajaxurl,
      data: {
        action: "wp_force_ssl_run_tool",
        _ajax_nonce: wp_force_ssl.nonce_run_tool,
        tool: "ssl_status",
        force: force,
      },
    })
      .always(function (data) {})
      .done(function (data) {
        if (data.success) {
          ssl_cert_info = data.data;
          ssl_cert_info_html = "";

          if (ssl_cert_info.error == true) {
            ssl_cert_info_html += 'Your SSL certificate is <strong class="wfssl-red">NOT</strong> valid.';
            ssl_cert_info_html += '<div class="ssl_cert_error">' + ssl_cert_info.data + "</div>";
            if (wp_force_ssl.is_localhost) {
              ssl_cert_info_html += '<div class="clear"><br /><strong>The site is not publicly available. It\'s on a localhost.</strong></div>';
            }
            ssl_cert_info_html += '<span class="wfssl-red dashicons dashicons-dismiss"></span>';
            $("#wfssl-cert-email").hide();
          } else {
            ssl_cert_info_html += 'Your SSL certificate is <strong class="wfssl-green">VALID</strong>.';
            ssl_cert_info_html += '<div class="ssl_cert_info"><strong>Issued To:</strong> ' + ssl_cert_info.data.issued_to + "</div>";
            ssl_cert_info_html += '<div class="ssl_cert_info"><strong>Issuer:</strong> ' + ssl_cert_info.data.issuer + "</div>";
            ssl_cert_info_html += '<div class="ssl_cert_info"><strong>Valid From:</strong> ' + ssl_cert_info.data.valid_from + "</div>";
            ssl_cert_info_html += '<div class="ssl_cert_info"><strong>Valid To:</strong> ' + ssl_cert_info.data.valid_to + "</div>";
            ssl_cert_info_html += '<span class="wfssl-green dashicons dashicons-yes-alt"></span>';
            $("#wfssl-cert-email").show();
          }

          ssl_cert_info_html += '<div class="button button-primary refresh-certificate-info" style="margin-top: 20px;">Refresh certificate info</div>';
          $("#ssl_cert_details").html(ssl_cert_info_html);

          refresh_tooltips();
        } else {
          wfssl_swal.fire({
            type: "error",
            title: wp_force_ssl.undocumented_error,
          });
        }
      })
      .fail(function (data) {
        wfssl_swal.fire({
          type: "error",
          title: wp_force_ssl.undocumented_error,
        });
      })
      .then(scanner_do_queue);
  }

  /* License Tab */

  // fix for enter press in license field
  $("#license-key").on("keypress", function (e) {
    if (e.which == 13) {
      e.preventDefault();
      $("#save-license").trigger("click");
      return false;
    }
  }); // if enter on license key field

  $(".settings_page_wp-force-ssl").on("click", "#deactivate-license", function (e) {
    e.preventDefault();
    button = this;
    block = block_ui($(button).data("text-wait"));
    wf_licensing_deactivate_licence_ajax("wfssl", $("#license-key").val(), button);
    return;
  });

  // validate license
  $(".settings_page_wp-force-ssl").on("click", "#save-license", function (e, deactivate) {
    e.preventDefault();
    button = this;
    safe_refresh = true;
    block = block_ui($(button).data("text-wait"));

    wf_licensing_verify_licence_ajax("wfssl", $("#license-key").val(), button);

    return false;
  }); // validate license

  $("#wfssl_keyless_activation").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;
    block = block_ui($(button).data("text-wait"));

    wf_licensing_verify_licence_ajax("wfssl", "keyless", button);
    return;
  });

  $("#wfssl_deactivate_license").on("click", function (e) {
    e.preventDefault();

    button = this;
    safe_refresh = true;

    wf_licensing_deactivate_licence_ajax("wfssl", $("#license-key").val(), button);
    return;
  });

  // open Help Scout Beacon
  $(".settings_page_wp-force-ssl").on("click", ".open-beacon", function (e) {
    e.preventDefault();

    Beacon("open");

    return false;
  });

  // init Help Scout beacon
  if (wp_force_ssl.is_plugin_page && wp_force_ssl.whitelabel != "1" && wp_force_ssl.rebranding === "0") {
    Beacon("config", {
      enableFabAnimation: false,
      display: {},
      contactForm: {},
      labels: {},
    });
    Beacon("prefill", {
      name: "\n\n\n" + wp_force_ssl.support_name,
      subject: "WP Force SSL PRO in-plugin support",
      email: "",
      text: "\n\n\n" + wp_force_ssl.support_text,
    });
    Beacon("init", "7c7f415e-fe36-49e7-ba2d-c065290e5502");
  }

  /* General Functions */

  function refresh_tooltips() {
    $(".tooltip").tooltipster({
      theme: ["tooltipster-punk", "tooltipster-wfssl"],
      delay: 0,
    });
  }

  refresh_tooltips();

  // display a message while an action is performed
  function block_ui(message) {
    tmp = wfssl_swal.fire({
      text: message,
      type: false,
      imageUrl: wp_force_ssl.icon_url,
      onOpen: () => {
        $(wfssl_swal.getImage()).addClass("wfssl_flicker");
      },
      imageWidth: 100,
      imageHeight: 100,
      imageAlt: message,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      showConfirmButton: false,
    });
  }
}); // onload
