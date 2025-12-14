<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class LRP_ChatGPT_Machine_Translator extends LRP_Machine_Translator {
    /**
     * Send request to OpenAI API
     *
     * @param string $source_language       Translate from language
     * @param string $language_code         Translate to language
     * @param array $strings_array          Array of string to translate
     *
     * @return array|WP_Error               Response
     */
    public function send_request( $source_language, $language_code, $strings_array ){
        $api_key = $this->get_api_key();
        
        // Prepare the prompt
        $strings_text = implode("\n", $strings_array);
        $prompt = "Translate the following text from {$source_language} to {$language_code}. Maintain the original formatting and placeholders. Return only the translated text, line by line corresponding to the input:\n\n" . $strings_text;

        $messages = array();

        // Add System Prompt if available
        $system_content = "";
        if ( !empty( $this->settings['chatgpt-system-prompt'] ) ) {
            $system_content .= $this->settings['chatgpt-system-prompt'] . "\n";
        }

        // Add Glossary if available
        if ( !empty( $this->settings['chatgpt-glossary'] ) ) {
            $system_content .= "Do not translate the following words or phrases: " . $this->settings['chatgpt-glossary'] . ".\n";
        }

        if ( !empty( $system_content ) ) {
            $messages[] = array(
                'role' => 'system',
                'content' => trim($system_content)
            );
        }

        $messages[] = array(
            'role' => 'user',
            'content' => $prompt
        );

        $body = array(
            'model' => 'gpt-3.5-turbo', // Or gpt-4
            'messages' => $messages
        );

        $response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode($body),
            'timeout' => 45
        ));

        return $response;
    }

    /**
     * Returns an array with the API provided translations of the $new_strings array.
     *
     * @param array $new_strings                    array with the strings that need translation.
     * @param string $target_language_code          language code of the language that we will be translating to.
     * @param string $source_language_code          language code of the language that we will be translating from.
     * @return array                                array with the translation strings and the preserved keys or an empty array if something went wrong
     */
    public function translate_array($new_strings, $target_language_code, $source_language_code = null ){
        if ( $source_language_code == null ){
            $source_language_code = $this->settings['default-language'];
        }

        if ( empty( $new_strings ) ) {
            return array();
        }

        $response = $this->send_request( $source_language_code, $target_language_code, $new_strings );

        if ( is_wp_error( $response ) ) {
            return array();
        }

        $body = wp_remote_retrieve_body( $response );
        $data = json_decode( $body, true );

        if ( isset( $data['choices'][0]['message']['content'] ) ) {
            $translated_text = $data['choices'][0]['message']['content'];
            $translated_strings = explode("\n", $translated_text);
            
            // Clean up and map back to original keys
            $result = array();
            $i = 0;
            foreach ( $new_strings as $key => $original_string ) {
                if ( isset( $translated_strings[$i] ) ) {
                    $result[$key] = trim( $translated_strings[$i] );
                }
                $i++;
            }
            return $result;
        }

        return array();
    }

    public function test_request(){
        return $this->send_request('en', 'es', array('Hello world'));
    }
}
