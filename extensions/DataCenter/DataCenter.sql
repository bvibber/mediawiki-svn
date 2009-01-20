-- 
-- SQL for DataCenter Extension
-- 
-- Facilities
-- 
-- Table of locations of physical datacenters
DROP TABLE IF EXISTS dc_facility_locations;
CREATE TABLE /*$wgDBPrefix*/dc_facility_locations (
    -- Unique ID of dc_locations
    fcl_loc_id INTEGER AUTO_INCREMENT,
    -- Tense of this rack
    fcl_loc_tense ENUM( 'past', 'present', 'future' ) default 'present',
    -- Name of this location
    fcl_loc_name VARBINARY(255) NOT NULL default '',
    -- Region of this location
    fcl_loc_region VARBINARY(255) NOT NULL default '',
    -- Latitude value of this location
    fcl_loc_latitude FLOAT(12,8),
    -- Longitude value of this location
    fcl_loc_longitude FLOAT(12,8),
    -- 
    PRIMARY KEY (fcl_loc_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of spaces in locations
DROP TABLE IF EXISTS dc_facility_spaces;
CREATE TABLE /*$wgDBPrefix*/dc_facility_spaces (
    -- Unique ID of dc_spaces
    fcl_spc_id INTEGER AUTO_INCREMENT,
    -- Tense of this rack
    fcl_spc_tense ENUM( 'past', 'present', 'future' ) default 'present',
    -- Name of this space
    fcl_spc_name VARBINARY(255) NOT NULL default '',
    -- ID of location this space is in
    fcl_spc_location INTEGER,
    -- Width of this space in meters
    fcl_spc_width INTEGER,
    -- Height of this space in meters
    fcl_spc_height INTEGER,
    -- Depth of this space in meters
    fcl_spc_depth INTEGER,
    -- Power capacity in watts
    fcl_spc_power INTEGER,
    -- 
    PRIMARY KEY (fcl_spc_id)
) /*$wgDBTableOptions*/;
-- 
-- Assets
-- 
-- Table of racks
DROP TABLE IF EXISTS dc_rack_assets;
CREATE TABLE /*$wgDBPrefix*/dc_rack_assets (
    -- Unique ID of dc_racks
    ast_rak_id INTEGER AUTO_INCREMENT,
    -- Tense of this racj
    ast_rak_tense ENUM( 'past', 'present', 'future' ) default 'present',
    -- ID of location this space is in
    ast_rak_location INTEGER,
    -- ID of model this rack is
    ast_rak_model INTEGER,
    -- Serial Number of rack
    ast_rak_serial VARBINARY(255) NOT NULL default '',
    -- Asset Number of rack
    ast_rak_asset VARBINARY(255) NOT NULL default '',
    -- 
    PRIMARY KEY (ast_rak_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of objects
DROP TABLE IF EXISTS dc_object_assets;
CREATE TABLE /*$wgDBPrefix*/dc_object_assets (
    -- Unique ID of dc_objects
    ast_obj_id INTEGER AUTO_INCREMENT,
    -- Tense of this rack
    ast_obj_tense ENUM( 'past', 'present', 'future' ) default 'present',
    -- ID of location this space is in
    ast_obj_location INTEGER,
    -- ID of model this object is of
    ast_obj_model INTEGER,
    -- Serial Number of object
    ast_obj_serial VARBINARY(255) NOT NULL default '',
    -- Asset Number of object
    ast_obj_asset VARBINARY(255) NOT NULL default '',
    -- 
    PRIMARY KEY (ast_obj_id)
) /*$wgDBTableOptions*/;
-- 
-- Models
-- 
-- Table of model of racks
DROP TABLE IF EXISTS dc_rack_models;
CREATE TABLE /*$wgDBPrefix*/dc_rack_models (
    -- Unique ID of dc_rack_models
    mdl_rak_id INTEGER AUTO_INCREMENT,
    -- Manufacturer name of this rack model
    mdl_rak_manufacturer VARBINARY(255) NOT NULL default '',
    -- Model name of this rack model
    mdl_rak_name VARBINARY(255) NOT NULL default '',
    -- Kind of this rack model
    mdl_rak_kind VARBINARY(255) NOT NULL default '',
    -- Note about this rack model
    mdl_rak_note BLOB,
    -- Number of vertical rack units this rack model can hold
    mdl_rak_units INTEGER,
    -- 
    PRIMARY KEY (mdl_rak_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of model of objects
DROP TABLE IF EXISTS dc_object_models;
CREATE TABLE /*$wgDBPrefix*/dc_object_models (
    -- Unique ID of dc_object_models
    mdl_obj_id INTEGER AUTO_INCREMENT,
    -- Manufacturer name of this object model
    mdl_obj_manufacturer VARBINARY(255) NOT NULL default '',
    -- Model name of this object model
    mdl_obj_name VARBINARY(255) NOT NULL default '',
    -- Kind of this object model
    mdl_obj_kind VARBINARY(255) NOT NULL default '',
    -- Note about this object model
    mdl_obj_note BLOB,
    -- The form factor of this object model
    mdl_obj_form_factor ENUM(
        'rackunit',
        'module',
        'desktop',
        'portable'
    ),
    -- Height of object in vertical rack units (if rack-mountable)
    mdl_obj_units INTEGER,
    -- Depth of object in fractions (1/4) of total rack depth
    mdl_obj_depth INTEGER,
    --  Power consumption of this object
    mdl_obj_power INTEGER,
    -- 
    PRIMARY KEY (mdl_obj_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of models of ports
DROP TABLE IF EXISTS dc_port_models;
CREATE TABLE /*$wgDBPrefix*/dc_port_models (
    -- Unique ID of dc_port_models
    mdl_prt_id INTEGER AUTO_INCREMENT,
    -- Name of this port model
    mdl_prt_name VARBINARY(255) NOT NULL default '',
    -- Kind of this port model
    mdl_prt_kind VARBINARY(255) NOT NULL default '',
    -- Note about this port model
    mdl_prt_note BLOB,
    -- Category of this port model
    mdl_prt_category ENUM(
        'network',
        'power',
        'audio',
        'video',
        'sensor',
        'serial',
        'other'
    ) NOT NULL default 'other',
    -- Format of this port model
    mdl_prt_format ENUM(
        'digital',
        'analog',
        'mixed',
        'virtual',
        'none'
    ) NOT NULL default 'none',
    -- 
    PRIMARY KEY (mdl_prt_id)
) /*$wgDBTableOptions*/;
-- 
-- Meta Information
-- 
-- Table of tags used to categorize repairs
DROP TABLE IF EXISTS dc_meta_tags;
CREATE TABLE /*$wgDBPrefix*/dc_meta_tags (
    -- Unique ID of dc_tags
    mta_tag_id INTEGER AUTO_INCREMENT,
    -- Name of this tag
    mta_tag_name VARBINARY(255) NOT NULL default '',
    -- 
    PRIMARY KEY (mta_tag_id)
) /*$wgDBTableOptions*/;
-- 
-- 
DROP TABLE IF EXISTS dc_meta_plans;
CREATE TABLE /*$wgDBPrefix*/dc_meta_plans (
    -- Unique ID of dc_meta_plans
    mta_pln_id INTEGER AUTO_INCREMENT,
    -- Name of this plan
    mta_pln_name VARBINARY(255) NOT NULL default '',
    -- Tense of this link
    mta_pln_tense ENUM( 'past', 'present', 'future' ) default 'present',
    -- ID of space of this plan
    mta_pln_space INTEGER,
    -- Note for this plan
    mta_pln_note BLOB,
    -- 
    PRIMARY KEY (mta_pln_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of changes made to targets (locations, spaces, racks, objects,
-- ports, or connections)
DROP TABLE IF EXISTS dc_meta_changes;
CREATE TABLE /*$wgDBPrefix*/dc_meta_changes (
    -- Unique ID of dc_meta_changes
    mta_cng_id INTEGER AUTO_INCREMENT,
    -- ID of user who made change
    mta_cng_user INTEGER,
    -- Timestamp of when change was made
    mta_cng_timestamp BINARY(14),
    -- ID of component change was made to
    mta_cng_component_id INTEGER,
    -- Category of component type
    mta_cng_component_category ENUM(
        'facility',
        'asset',
        'model'
    ),
    -- Type of component change was made to
    mta_cng_component_type ENUM(
        'location',
        'space',
        'rack',
        'object',
        'port',
        'connection'
    ),
    -- Type of change that was made
    mta_cng_type VARBINARY(255),
    -- Text from user about change
    mta_cng_note TINYBLOB,
    -- Serialized php of row array after change
    mta_cng_state BLOB,
    -- 
    PRIMARY KEY (mta_cng_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of models used by other models
DROP TABLE IF EXISTS dc_model_links;
CREATE TABLE /*$wgDBPrefix*/dc_model_links (
    -- Unique ID of dc_meta_model_links
    lnk_mdl_id INTEGER AUTO_INCREMENT,
    -- Name of this link
    lnk_mdl_name VARBINARY(255) NOT NULL default '',
    -- Type of parent model using this link
    lnk_mdl_parent_type ENUM(
        'object'
    ),
    -- ID of parent model using this link
    lnk_mdl_parent_id INTEGER,
    -- Type of child model this link attaches
    lnk_mdl_child_type ENUM(
        'object',
        'port'
    ),
    -- ID of child model this link attaches
    lnk_mdl_child_id INTEGER,
    -- Quantity of attached models
    lnk_mdl_quantity INTEGER,
    -- 
    PRIMARY KEY (lnk_mdl_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of assets used by other assets
DROP TABLE IF EXISTS dc_asset_links;
CREATE TABLE /*$wgDBPrefix*/dc_asset_links (
    -- Unique ID of dc_meta_asset_links
    lnk_ast_id INTEGER AUTO_INCREMENT,
    -- Name of this asset
    lnk_ast_name VARBINARY(255) NOT NULL default '',
    -- ID of plan this link is a part of
    lnk_ast_plan INTEGER,
    -- Tense of this link
    lnk_ast_tense ENUM( 'past', 'present', 'future' ) default 'present',
    -- ID of parent asset link this link is a part of
    lnk_ast_parent_link INTEGER,
    -- Type of child asset this link attaches
    lnk_ast_asset_type ENUM(
        'rack',
        'object'
    ),
    -- ID of child asset this link attaches
    lnk_ast_asset_id INTEGER,
    -- X Position of child asset in parent asset
    lnk_ast_x INTEGER,
    -- Y Position of child asset in parent asset
    lnk_ast_y INTEGER,
    -- Z Position of child asset in parent asset
    lnk_ast_z INTEGER,
    -- Orientation of child asset in parent asset
    lnk_ast_orientation INTEGER,
    -- 
    PRIMARY KEY (lnk_ast_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of meta fields to attach to components
DROP TABLE IF EXISTS dc_field_links;
CREATE TABLE /*$wgDBPrefix*/dc_field_links (
    -- Unique ID of dc_meta_field_links
    lnk_fld_id INTEGER AUTO_INCREMENT,
    -- ID of field to attach
    lnk_fld_field INTEGER,
    -- Category of component type
    lnk_fld_component_category ENUM(
        'facility',
        'asset',
        'model'
    ),
    -- Type of component to attach field to
    lnk_fld_component_type ENUM(
        'location',
        'space',
        'rack',
        'object',
        'port',
        'connection'
    ),
    -- 
    PRIMARY KEY (lnk_fld_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of meta fields to attach to models
DROP TABLE IF EXISTS dc_meta_fields;
CREATE TABLE /*$wgDBPrefix*/dc_meta_fields (
    -- Unique ID of dc_meta_fields
    mta_fld_id INTEGER AUTO_INCREMENT,
    -- Name of this meta field
    mta_fld_name VARBINARY(255) NOT NULL default '',
    -- Format of this meta information
    mta_fld_format ENUM(
        'tag',
        'text',
        'string',
        'number',
        'boolean'
    ),
    -- 
    PRIMARY KEY (mta_fld_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of meta information to attach to assets
DROP TABLE IF EXISTS dc_meta_values;
CREATE TABLE /*$wgDBPrefix*/dc_meta_values (
    -- Unique ID of dc_meta_values
    mta_val_id INTEGER AUTO_INCREMENT,
    -- Category of component type
    mta_val_component_category ENUM(
        'facility',
        'asset',
        'model'
    ),
    -- Type of component to attach field to
    mta_val_component_type ENUM(
        'location',
        'space',
        'rack',
        'object',
        'port',
        'connection'
    ),
    -- ID of component this meta information is for
    mta_val_component_id INTEGER,
    -- ID of meta field this meta information represents
    mta_val_field INTEGER,
    -- Value of this meta information
    mta_val_value BLOB,
    -- 
    PRIMARY KEY (mta_val_id)
) /*$wgDBTableOptions*/;
-- 
-- Table of connections between ports
DROP TABLE IF EXISTS dc_meta_connections;
CREATE TABLE /*$wgDBPrefix*/dc_meta_connections (
    -- Unique ID of dc_connections
    mta_con_id INTEGER AUTO_INCREMENT,
    -- ID of port being connected from
    mta_con_port_a INTEGER,
    -- ID of port being connected to
    mta_con_port_b INTEGER,
    -- 
    PRIMARY KEY (mta_con_id)
) /*$wgDBTableOptions*/;
-- 
--  Initial Facilities
-- 
INSERT INTO dc_facility_locations
    (
        fcl_loc_tense, fcl_loc_name, fcl_loc_region, fcl_loc_latitude,
        fcl_loc_longitude
    )
    VALUES
        ( 'present', 'PMTPA', 'Tampa, FL', 27.949036, -82.457306 ),
        ( 'present', 'SFO', 'San Francisco, CA', 37.782792, -122.394810 );
-- 
INSERT INTO dc_facility_spaces
    (
        fcl_spc_tense, fcl_spc_name, fcl_spc_location, fcl_spc_width,
        fcl_spc_height, fcl_spc_depth,  fcl_spc_power
    )
    VALUES
        ( 'present', 'Server Room', 1, 3, 3, 7, 1000 ),
        ( 'present', 'Data Closet', 2, 3, 3, 5, 200 );
-- 
--  Initial Assets
-- 
INSERT INTO dc_rack_assets
    (
        ast_rak_model, ast_rak_tense, ast_rak_location, ast_rak_asset,
        ast_rak_serial
    )
    VALUES
        ( 2, 'present', 1, '1230112301', '1Q2W3E4R5T6Y7U' ),
        ( 2, 'present', 1, '2340223402', '1A2S3D4F5G6H7J' ),
        ( 2, 'present', 1, '3450334503', '1Z2X3C4V5B6N7M' ),
        ( 2, 'present', 1, '4560445604', 'Q1W2E3R4T5Y6U7' ),
        ( 2, 'present', 1, '5670556705', 'A1S2D3F4G5H6J7' ),
        ( 2, 'present', 2, '6780667806', 'Z1X2C3V4B5N6M7' );
-- 
INSERT INTO dc_object_assets
    (
        ast_obj_model, ast_obj_tense, ast_obj_location, ast_obj_asset,
        ast_obj_serial
    )
    VALUES
        ( 1, 'present', 2, '1230112301', '1Q2W3E4R5T6Y7U' ),
        ( 1, 'present', 2, '2340223402', '1A2S3D4F5G6H7J' ),
        ( 1, 'present', 2, '3450334503', '1Z2X3C4V5B6N7M' ),
        ( 1, 'present', 2, '4560445604', 'Q1W2E3R4T5Y6U7' ),
        ( 2, 'present', 2, '5670556705', 'A1S2D3F4G5H6J7' ),
        ( 3, 'present', 2, '6780667806', 'Z1X2C3V4B5N6M7' );
-- 
-- Initial Models
-- 
INSERT INTO dc_rack_models
    (
        mdl_rak_manufacturer, mdl_rak_name, mdl_rak_kind, mdl_rak_units
    )
    VALUES
        ( 'Rittal', 'TS-42', 'Rack', 42 ),
        ( 'Rittal', 'TS-44', 'Rack', 44 ),
        ( 'Rittal', 'TS-47', 'Rack', 47 );
-- 
INSERT INTO dc_object_models
    (
        mdl_obj_manufacturer, mdl_obj_name, mdl_obj_kind, mdl_obj_form_factor,
        mdl_obj_units, mdl_obj_depth, mdl_obj_power
    )
    VALUES
        ( 'Sun', 'Cobalt RaQ XTR', 'Web Server', 'rackunit', 2, 3, 400 ),
        ( 'Cisco', 'Catalyst 2950', 'Switch', 'rackunit', 1, 1, 100 ),
        ( 'APC', 'Smart-UPS 3000VA', 'UPS', 'rackunit', 5, 2, 20 );
-- 
INSERT INTO dc_port_models
    (
        mdl_prt_name, mdl_prt_kind, mdl_prt_category, mdl_prt_format
    )
    VALUES
        ( '1000Base-ZX', 'Ethernet', 'network', 'digital' ),
        ( '10GBase-CX4', 'Ethernet', 'network', 'digital' ),
        ( '10GBase-ER', 'Ethernet', 'network', 'digital' ),
        ( '10GBase-Kx', 'Ethernet', 'network', 'digital' ),
        ( '10GBase-LR', 'Ethernet', 'network', 'digital' ),
        ( '10GBase-LRM', 'Ethernet', 'network', 'digital' ),
        ( '10GBase-LX4', 'Ethernet', 'network', 'digital' ),
        ( '10GBase-ZR', 'Ethernet', 'network', 'digital' ),
        ( 'LC/1000Base-LX', 'Ethernet', 'network', 'digital' ),
        ( 'LC/1000Base-SX', 'Ethernet', 'network', 'digital' ),
        ( 'LC/100Base-FX', 'Ethernet', 'network', 'digital' ),
        ( 'LC/100Base-SX', 'Ethernet', 'network', 'digital' ),
        ( 'LC/10GBase-SR', 'Ethernet', 'network', 'digital' ),
        ( 'RJ-45/1000Base-T', 'Ethernet', 'network', 'digital' ),
        ( 'RJ-45/100Base-TX', 'Ethernet', 'network', 'digital' ),
        ( 'RJ-45/10Base-T', 'Ethernet', 'network', 'digital' ),
        ( 'SC/1000Base-LX', 'Ethernet', 'network', 'digital' ),
        ( 'SC/1000Base-SX', 'Ethernet', 'network', 'digital' ),
        ( 'SC/100Base-FX', 'Ethernet', 'network', 'digital' ),
        ( 'SC/100Base-SX', 'Ethernet', 'network', 'digital' ),
        ( 'BNC/10Base2', 'Token Ring', 'network', 'digital' ),
        ( 'RS-232', 'Serial', 'serial', 'digital' ),
        ( 'RS-442', 'Serial', 'serial', 'digital' ),
        ( 'IEC 120v', 'Power', 'power', 'none' ),
        ( 'IEC 240v', 'Power', 'power', 'none' ),
        ( 'Type A 120v', 'Power', 'power', 'none' ),
        ( 'Type B 120v', 'Power', 'power', 'none' ),
        ( 'Type C 240v', 'Power', 'power', 'none' ),
        ( 'Type F 240v', 'Power', 'power', 'none' ),
        ( 'PS2', 'User Input', 'serial', 'digital' ),
        ( 'USB 1.0', 'USB', 'serial', 'digital' ),
        ( 'USB 2.0', 'USB', 'serial', 'digital' ),
        ( 'IEEE 1394a', 'FireWire', 'serial', 'digital' ),
        ( 'IEEE 1394b', 'FireWire', 'serial', 'digital' ),
        ( 'IEEE 1394c', 'FireWire', 'serial', 'digital' ),
        ( 'VGA', 'Video', 'video', 'analog' ),
        ( 'DVI', 'Video', 'video', 'digital' ),
        ( 'MiniDVI', 'Video', 'video', 'digital' ),
        ( 'MicroDVI', 'Video', 'video', 'digital' ),
        ( 'Optical Audio', 'Audio', 'audio', 'digital' ),
        ( 'Analog Audio', 'Audio', 'audio', 'analog' ),
        ( 'Hybrid Audio', 'Audio', 'audio', 'mixed' ),
        ( 'Other', 'Port', 'other', 'none' );
-- 
-- Initial Meta
-- 
INSERT INTO dc_meta_fields
    (
        mta_fld_name, mta_fld_format
    )
    VALUES
        ( 'WikiMedia Owned', 'boolean' ),
        ( 'Extra Notes', 'text' ),
        ( 'Weight (LBS)', 'number' );
-- 
INSERT INTO dc_meta_values
    (
        mta_val_component_category, mta_val_component_type,
        mta_val_component_id, mta_val_field, mta_val_value
    )
    VALUES
        ( 'facility', 'location', 2, 1, true ),
        ( 'facility', 'location', 2, 2, 'The best place to be!' );
--
INSERT INTO dc_meta_plans
    (
        mta_pln_tense, mta_pln_space, mta_pln_name, mta_pln_note
    )
    VALUES
    ( 'present', 1, 'PMTA 1.0', 'Current configuration' ),
    ( 'present', 2, 'SFO 1.0', 'Incomplete but current' ),
    ( 'future', 1, 'PMTA 1.1', 'Upcoming configuration' );
-- 
-- Initial Links
-- 
INSERT INTO dc_asset_links
    (
        lnk_ast_name, lnk_ast_plan, lnk_ast_tense, lnk_ast_parent_link,
        lnk_ast_asset_type, lnk_ast_asset_id, lnk_ast_x, lnk_ast_y, lnk_ast_z,
        lnk_ast_orientation
    )
    VALUES
        ( 'Rack 1A', 1, 'present', null, 'rack', 1, 2, 1, null, 1 ),
        ( 'Rack 1B', 1, 'present', null, 'rack', 2, 2, 2, null, 1 ),
        ( 'Rack 1C', 1, 'present', null, 'rack', 3, 2, 3, null, 1 ),
        ( 'Rack 1D', 1, 'present', null, 'rack', 4, 2, 4, null, 1 ),
        ( 'Rack 1E', 1, 'present', null, 'rack', 5, 2, 5, null, 1 ),
        ( 'Rack 1A', 2, 'present', null, 'rack', 6, 3, 4, null, 1 ),
        ( 'Web Server A', 2, 'present', 6, 'object', 1, null, null, 1, 0 ),
        ( 'Web Server B', 2, 'present', 6, 'object', 2, null, null, 3, 0 ),
        ( 'Web Server C', 2, 'present', 6, 'object', 3, null, null, 5, 0 ),
        ( 'Web Server D', 2, 'present', 6, 'object', 4, null, null, 7, 0 ),
        ( 'Switch', 2, 'present', 6, 'object', 5, null, null, 9, 0 ),
        ( 'UPS', 2, 'present', 6, 'object', 6, null, null, 10, 0 );
-- 
INSERT INTO dc_model_links
    (
        lnk_mdl_name, lnk_mdl_quantity, lnk_mdl_parent_type,
        lnk_mdl_parent_id, lnk_mdl_child_type, lnk_mdl_child_id
    )
    VALUES
        ( 'Ethernet', 2, 'object', 1, 'port', 14 ),
        ( 'Ethernet', 24, 'object', 2, 'port', 14 ),
        ( 'Power In', 1, 'object', 3, 'port', 27 ),
        ( 'Power Out', 8, 'object', 3, 'port', 24 );
-- 
INSERT INTO dc_field_links
    (
        lnk_fld_field, lnk_fld_component_category, lnk_fld_component_type
    )
    VALUES
        ( 1, 'facility', 'location' ),
        ( 1, 'facility', 'space' ),
        ( 1, 'asset', 'rack' ),
        ( 1, 'asset', 'object' ),
        ( 2, 'facility', 'location' ),
        ( 2, 'facility', 'space' ),
        ( 2, 'asset', 'rack' ),
        ( 2, 'asset', 'object' ),
        ( 3, 'asset', 'object' );
-- 
