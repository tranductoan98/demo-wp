/* eslint-disable jsx-a11y/anchor-is-valid */
import { Card, message, Skeleton, Space, Tooltip, Spin } from "antd";
import {
  CheckSquareOutlined,
  WarningOutlined,
  IssuesCloseOutlined,
  CheckCircleOutlined,
  CloseCircleOutlined,
  LoadingOutlined
} from "@ant-design/icons";
import "./style.scss";

// Redux
import { dashboardActions } from "store/actions";
import { bindActionCreators, Dispatch } from "redux";
import { connect } from "react-redux";
import { useState } from "react";

const { activatePlugin, recheckPluginStatus } = dashboardActions;

const LicenseStatus = (props: any) => {
  const {
    i18n,
    license_page_link,
    license_statuses,
    wws_plugins,
    fetching,
    actions
  } = props;

  const [activating, setActivating] = useState(false);
  const [pluginName, setPluginName] = useState("");

  const [fetchingPluginStatus, setFetchingPluginStatus] = useState(false);

  let deactivatedCounter = 0;

  // Installed but deactivated plugins
  let installedPlugins = Object.values(wws_plugins).filter((data: any) => {
    return data?.installed && !data?.active ? true : false;
  });

  // Get all not deactivated plugins
  let filteredPlugins = Object.values(license_statuses).filter((data: any) => {
    return data?.status !== "deactivated" ? true : false;
  });

  const activatePlugin = (key: string) => {
    setActivating(true);
    setPluginName(key);
    actions.activatePlugin({
      plugin_name: key,
      successCB: (response: any) => {
        setActivating(false);
        message.success(response?.message);
        setTimeout(function () {
          window.location.reload();
        }, 1000);
      },
      failCB: (response: any) => {
        setActivating(false);
        message.error(response?.message);
        setTimeout(function () {
          window.location.reload();
        }, 1000);
      }
    });
  };

  const checkPluginStatuses = () => {
    setFetchingPluginStatus(true);
    actions.recheckPluginStatus({
      successCB: (response: any) => {
        setFetchingPluginStatus(false);
        message.success(response?.message);
      },
      failCB: (response: any) => {
        setFetchingPluginStatus(false);
        message.error(response?.message);
      }
    });
  };

  if (fetching)
    // Loading / Skeleton
    return (
      <Card className="license-status">
        <h3>
          <Skeleton.Button
            style={{ width: 150, height: 30 }}
            active={true}
            size="large"
          />
        </h3>
        <ul>
          {[1, 2, 3].map((key: number) => {
            return (
              <li key={key}>
                <Skeleton.Button
                  style={{ width: 200, height: 30 }}
                  active={true}
                  size="large"
                />
              </li>
            );
          })}
        </ul>
      </Card>
    );
  else if (license_statuses.length === 0)
    // Licenses are Active
    return (
      <Card className="license-status">
        <h3>{i18n?.wholesale_suite_plugins}</h3>
        <ul>
          {Object.keys(wws_plugins).map((key: string) => {
            return (
              <li key={key}>
                <CheckCircleOutlined style={{ color: "green" }} />
                &nbsp;
                <a
                  href={wws_plugins?.[key]?.link}
                  target="_blank"
                  rel="noreferrer"
                >
                  {wws_plugins?.[key]?.name}
                </a>
              </li>
            );
          })}
        </ul>
      </Card>
    );
  else
    return (
      <Space direction="vertical" size="large">
        {filteredPlugins.length > 0 ? (
          <Card className="license-status">
            <h3>{i18n?.license_activation_status}</h3>
            <ul>
              {filteredPlugins.map((data: any, index: number) => {
                if (data?.status === "inactive") {
                  return (
                    <li key={index}>
                      <IssuesCloseOutlined style={{ color: "orange" }} />
                      <span
                        dangerouslySetInnerHTML={{
                          __html: ` ${data?.text}`
                        }}
                      ></span>
                    </li>
                  );
                } else if (data?.status === "expired") {
                  return (
                    <li key={index}>
                      <WarningOutlined style={{ color: "red" }} />
                      <span
                        dangerouslySetInnerHTML={{
                          __html: ` ${data?.text}`
                        }}
                      ></span>
                    </li>
                  );
                } else if (data?.status === "invalid") {
                  return (
                    <li key={index}>
                      <CloseCircleOutlined style={{ color: "#ffc107" }} />
                      <Tooltip placement="right" title={data?.tooltip}>
                        <span
                          dangerouslySetInnerHTML={{
                            __html: ` ${data?.text}`
                          }}
                        ></span>
                      </Tooltip>
                    </li>
                  );
                }
                // Licenses is active
                return (
                  <li key={index}>
                    <CheckSquareOutlined style={{ color: "green" }} />
                    <span
                      dangerouslySetInnerHTML={{
                        __html: ` ${data?.text}`
                      }}
                    ></span>
                  </li>
                );
              })}

              {deactivatedCounter !== Object.keys(license_statuses).length ? (
                <>
                  <li>
                    <a
                      href={license_page_link}
                      dangerouslySetInnerHTML={{ __html: i18n?.view_licenses }}
                    ></a>
                  </li>
                  <li>
                    <Tooltip
                      placement="right"
                      title={i18n?.recheck_plugin_status_tooltip}
                    >
                      <a href="#" onClick={() => checkPluginStatuses()}>
                        {i18n?.recheck_plugin_status}
                      </a>
                      <LoadingOutlined
                        style={{
                          marginLeft: "10px",
                          display: fetchingPluginStatus
                            ? "inline-block"
                            : "none"
                        }}
                        spin
                      />
                    </Tooltip>
                  </li>
                </>
              ) : (
                ""
              )}
            </ul>
          </Card>
        ) : (
          <></>
        )}

        {installedPlugins.length > 0 ? (
          <Card className="license-status">
            <h3>{i18n?.deactivated_plugins}</h3>
            <ul>
              {installedPlugins.map((data: any, index: number, test: any) => {
                return (
                  <li key={index}>
                    <span
                      dangerouslySetInnerHTML={{
                        __html: ` ${data?.name} `
                      }}
                    ></span>
                    <Tooltip placement="right" title={i18n?.click_to_activate}>
                      <a href="#" onClick={() => activatePlugin(data?.key)}>
                        ({i18n?.activate_plugin})
                        <LoadingOutlined
                          style={{
                            marginLeft: "10px",
                            display:
                              pluginName === data?.key && activating
                                ? "inline-block"
                                : "none"
                          }}
                          spin
                        />
                      </a>
                    </Tooltip>
                  </li>
                );
              })}
            </ul>
          </Card>
        ) : (
          <></>
        )}
      </Space>
    );
};

const mapStateToProps = (store: any, props: any) => ({
  fetching: store?.dashboard?.fetching,
  i18n: store?.dashboard?.internationalization,
  license_page_link: store?.dashboard?.license_page_link,
  license_statuses: store?.dashboard?.license_statuses,
  wws_plugins: store?.dashboard?.wws_plugins
});

const mapDispatchToProps = (dispatch: Dispatch) => ({
  actions: bindActionCreators(
    {
      activatePlugin,
      recheckPluginStatus
    },
    dispatch
  )
});

export default connect(mapStateToProps, mapDispatchToProps)(LicenseStatus);
