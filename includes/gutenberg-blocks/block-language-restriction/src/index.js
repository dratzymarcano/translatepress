import { assign, has } from "lodash";
import { addFilter } from "@wordpress/hooks";
import { createHigherOrderComponent } from "@wordpress/compose";
import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody } from "@wordpress/components";

import ControlsCommon from './components/ControlsCommon'

/**
 * Add the language restriction inspector controls in the editor
 */
function LrpBlockContentRestrictionControls(props) {
    const { attributes, setAttributes } = props;
    const { LrpContentRestriction } = attributes;

    // Abort if the block type does not have the LrpContentRestriction attribute registered
    if ( !has(attributes, "LrpContentRestriction") )
        return null;

    return (
        <InspectorControls>
            <PanelBody
                title={__(
                    "LinguaPress Language Restriction",
                    "linguapress",
                )}
                className="linguapress-content-restriction-settings"
                initialOpen={LrpContentRestriction.panel_open}
                onToggle={(value) =>
                    setAttributes({
                        LrpContentRestriction: assign(
                            { ...LrpContentRestriction },
                            { panel_open: !LrpContentRestriction.panel_open },
                        ),
                    })
                }
            >
                <ControlsCommon {...props} />
            </PanelBody>
        </InspectorControls>
    );
}

/**
 * Add the content restriction settings attribute
 */
function LrpContentRestrictionAttributes( settings ) {
    let contentRestrictionAttributes = {
        LrpContentRestriction: {
            type: "object",
            properties: {
                restriction_type: {
                    type: "string",
                },
                selected_languages: {
                    type: "array"
                },
                panel_open: {
                    type: "bool",
                },
            },
            default: {
                restriction_type: "exclude",
                selected_languages: [],
                panel_open: true,
            },
        },
    };

    settings.attributes = assign(
        settings.attributes,
        contentRestrictionAttributes,
    );

    return settings;
}
addFilter(
    "blocks.registerBlockType",
    "linguapress/attributes",
    LrpContentRestrictionAttributes,
);

/**
 * Filter the block edit object and add content restriction controls
 */
const blockLrpContentRestrictionControls = createHigherOrderComponent(
    (BlockEdit) => {
        return (props) => {
            return (
                <>
                    <BlockEdit {...props} />
                    <LrpBlockContentRestrictionControls {...props} />
                </>
            );
        };
    },
    "blockLrpContentRestrictionControls",
);
addFilter(
    "editor.BlockEdit",
    "linguapress/inspector-controls",
    blockLrpContentRestrictionControls,
    100, // above Advanced controls
);
