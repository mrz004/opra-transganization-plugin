<?php
use Fpdf\Fpdf;

function send_pdf_email($email, $subject, $message, $file_path)
{

    $separator = md5(time());
    $headers = "MIME-Version: 1.0";
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"";
    $headers .= "Content-Transfer-Encoding: 7bit";
    wp_mail($email, $subject, $message, $headers, array($file_path));
    // wp_mail($email, $subject, $message, $headers);
}

function generate_pdf($mlw_quiz_array)
{
    // *** 1 in = 2.54 cm
    // *** A4 = 21 cm x 29.7 cm
    // use fpdf;
    // require_once MRZ_CRE_PLUGIN_PATH . "vendor/autoload.php";
    // require_once MRZ_CRE_PLUGIN_PATH . '/vendor/autoload.php';


    $categories = array("Value Proposition", "Customer Segments", "Channels", "Customer Relationships", "Revenue Streams", "Key Resources", "Key Activities", "Cost Structure");

    $line_height = 1;
    $docMargin = 25;

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);
    $pdf->SetMargins($docMargin, $docMargin, $docMargin);

    $docWidth = $pdf->GetPageWidth();
    $docHeight = $pdf->GetPageHeight();
    $paraWidth = $docWidth - ($docMargin * 2);
    $paraHeight = 4;
    $separator_width = .3;
    $logoWidthRatio = 1865;
    $logoHeightRatio = 1007;
    $logoWidth = ($docWidth * 2) / 5;
    $logoHeight = $logoHeightRatio * ($logoWidth / $logoWidthRatio);

    // // *** Adding Logo
    // $pdf->Image(MRZ_CRE_PLUGIN_PATH . 'assets/logo.jpg', ($docWidth - $logoWidth) / 2, null, $logoWidth, $logoHeight);
    // $pdf->Ln(4);

    // *** Adding Cover Page
    $pdf->Image(MRZ_CRE_PLUGIN_PATH . 'assets/cover_page.jpg', 0, 0, $docWidth, $docHeight);
    $pdf->Ln(4);
    $pdf->AddPage();

    // // *** Adding Title
    // $pdf->SetFont('Arial', 'B', 11);
    // $pdf->Cell($paraWidth, 0, 'ASSESSMENT REPORT', 0, 1, 'C');
    // $pdf->Ln(10);

    // // *** Adding tag line
    // $pdf->SetFont('Arial', '', 9);
    // $pdf->Cell($paraWidth, 0, 'Business Model Assessment Report By TransGanization', 0, 1, 'C');
    // $pdf->Ln(10);

    // *** Adding user details
    // ? Sample Formate
    // Company Name: Drifters Brewing company
    // Email ID: sheetalpokal@gmail.com
    // Business Model Assessment Report: Sheetal Shah

    // $phone = get_property_value($mlw_quiz_array, 'user_phone');
    // $business = get_property_value($mlw_quiz_array, 'user_business');
    // $unique_id = get_property_value($mlw_quiz_array, 'result_unique_id');

    $field_box_width = 60;
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($field_box_width, 0, 'Company Name: ', 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 0, get_property_value($mlw_quiz_array, 'user_business'), 0, 1);
    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($field_box_width, 0, 'Email Id: ', 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 0, get_property_value($mlw_quiz_array, 'user_email'), 0, 1);
    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($field_box_width, 0, 'Business Model Assessment Report: ', 0, 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(0, 0, get_property_value($mlw_quiz_array, 'user_name'), 0, 1);
    $pdf->Ln(6);

    // *** Adding Separator
    $pdf->SetLineWidth($separator_width);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Line($docMargin / 2, $pdf->GetY(), $docWidth - $docMargin / 2, $pdf->GetY());
    $pdf->Ln(6);

    // *** Adding intro
    $intro_text = 'This report is based on the information you\'ve given and it is indicative. The report is meant to indicate certain corrections needed in your business model. To fully understand and make it contextual, more detailed analysis is needed. However, it can still help you take proactive steps to strengthen your business model.';
    $pdf->SetFont('Arial', '', 9);
    // $intro_height = ceil($pdf->GetStringWidth($intro_text) / $paraWidth) * $line_height;
    $pdf->MultiCell($paraWidth, $paraHeight, $intro_text, 0, 'J');
    $pdf->Ln(10);

    // *** Adding Separator
    $pdf->SetLineWidth($separator_width);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Line($docMargin / 2, $pdf->GetY(), $docWidth - $docMargin / 2, $pdf->GetY());
    $pdf->Ln(6);

    foreach ($categories as $category) {
        $text = get_category_remark($category, $mlw_quiz_array);
        // $text = str_replace("\n", " ", $text);

        // $pdf->SetFont('Arial', '', 12);
        // $pdf->SetFillColor(0, 0, 255);
        // $pdf->SetFillColor(159, 197, 232);
        // $pdf->MultiCell($paraWidth, $paraHeight + 2, $category, 0, '', 1);
        // $pdf->Ln(4);

        $pdf->SetFont('Arial', '', 9);
        // $height = ceil($pdf->GetStringWidth($text) / $paraWidth) * $line_height;
        // $height = ceil($pdf->GetStringWidth($text) / $paraWidth);
        // $height = ceil($pdf->GetStringWidth($text) / $pdf->GetPageWidth());
        // $height = ceil($pdf->GetStringWidth($text) / $paraWidth);
        $pdf->MultiCell($paraWidth, $paraHeight, $text, 0, '');
        $pdf->Ln(10);
    }

    $filename = MRZ_CRE_PLUGIN_PATH . get_property_value($mlw_quiz_array, 'result_unique_id') . '.pdf';
    $pdf->Output('F', $filename);


    return $filename;
}

/*
 *    Replaces variable %CATEGORY_REMARK% with the points for that category
 *
 * Filter function that replaces variable %CATEGORY_REMARK% with the points from the category inside the variable tags. i.e. %CATEGORY_REMARK%category 1%/CATEGORY_REMARK%
 *
 * @since 4.0.0
 * @param string $content The contents of the results page
 * @param array $mlw_quiz_array The array of all the results from user taking the quiz
 * @return string Returns the contents for the results page
 */
function filter_custom_qsm_variable($content, $mlw_quiz_array)
{
    while (false !== strpos($content, '%CATEGORY_REMARK_')) {
        $category_name = mlw_qmn_get_string_between($content, '%CATEGORY_REMARK_', '%');
        $remark = get_category_remark($category_name, $mlw_quiz_array);
        $content = str_replace('%CATEGORY_REMARK_' . $category_name . '%', $remark, $content);
    }
    return $content;
}

function get_property_value($mlw_quiz_array, $property_name)
{
    return isset($mlw_quiz_array[$property_name]) ? html_entity_decode($mlw_quiz_array[$property_name]) : '';
}

function get_category_points($category_name, $mlw_quiz_array)
{
    $return_points = 0;
    $total_questions = 0;

    foreach ($mlw_quiz_array['question_answers_array'] as $answer) {
        if (is_array($answer['multicategories'])) {
            foreach ($answer['multicategories'] as $category) {
                $category_name_object = get_term_by('id', $category, 'qsm_category');
                if ($category_name_object->name == $category_name && '11' !== $answer['question_type']) {
                    $total_questions += 1;
                    $return_points += $answer['points'];
                }
            }
        }
    }
    return $return_points;
}

function get_category_remark($category_name, $mlw_quiz_array)
{
    $category_points = get_category_points($category_name, $mlw_quiz_array);
    if ($category_name == "Value Proposition") {
        if (8 >= $category_points) {
            $remark = <<<EOD
There is threat of misalignment and you may provide easy access to competitors
It seems your industry has gone through very fast paced changes in last couple of year, either technology or new entransta have created lot of disruption and the value proposition you currently hold has or has started becoming irrelevant , There is need for complete relook at your Value proposition from the market perspective. Customer Needs, Competetion and Target market needs to be re-evelauated. working on your value proposition will help a lot in building a great Business Model. 
an ineffective value proposition is characterized by fundamental flaws that undermine its credibility and relevance. It may overpromise and underdeliver, making unrealistic claims that fail to align with the actual product or service offering. Bad propositions often result in customer dissatisfaction, negative reviews, and eroded trust in the brand. Businesses with bad propositions may encounter significant hurdles in overcoming reputation damage and regaining consumer confidence, leading to decreased sales, profitability, and long-term viability.

You can learn benefits of having great value proposition by booking a consultation meeting with Transganization. ...
EOD;
        } elseif (11 >= $category_points) {
            $remark = <<<EOD
There is possibility of market misalignment and if not misalignment there is scope for area of distisfaction at both cutomer and your end
There exists an opportunity for enhancement within your current value proposition. Exploring various aspects of your value proposition could significantly elevate your potential for achieving greater success. Consider investigating the gap between your understanding of customer needs, evaluating competitor delivery styles, or reassessing your target market. Improving your value proposition holds the potential to unlock new avenues of opportunity for your business.

an average value proposition lacks clarity, fails to differentiate from competitors, and fails to inspire customers. It may offer vague promises or fail to articulate the unique value that sets the product or service apart. Mediocre propositions often lead to lukewarm responses from consumers, as they struggle to perceive the compelling reason to choose one brand over another. As a result, businesses with mediocre propositions may face challenges in attracting and retaining customers, experiencing stagnant growth and dwindling market share.

Note:If you require assistance in refining your value proposition and effectively positioning it in the market, we invite you to book a consultation slot with Transganization. Our team stands ready to offer guidance and support in enhancing your business strategy and maximizing your competitive edge.
EOD;
        } else {
            $remark = <<<EOD
Your Answers shows you have great value proposition, that can help you command good price for your product and services.
Your value proposition is robust, suggesting significant potential for exponential growth in both volume and revenue. Moreover, profitability should ideally meet or surpass industry standards. Your current trajectory indicates commendable performance. 
A great value proposition resonates deeply with customers, compelling them to choose a particular product or service over alternatives. It succinctly communicates the benefits and solutions offered, addressing key pain points and desires of the target audience. For example, Apple's iconic ""Think Different"" campaign conveyed innovation, creativity, and individuality, appealing to consumers seeking cutting-edge technology and design. The effects of a great value proposition are manifold: it fosters customer loyalty, drives sales, enhances brand reputation, and cultivates a competitive advantage in the market maintaining the relevance of your existing value proposition is advisable.
Note: However, if despite possessing such a compelling value proposition, your revenue, volume, or profitability fall short of expectations, then there must be other areas of business operations that you need to carefully evaluate. Feel free to book a consultation slot with Transganization.
EOD;
        }
    } elseif ($category_name == "Customer Segments") {
        if (8 >= $category_points) {
            $remark = <<<EOD
If you're an ambitious entrepreneur, this situation can be incredibly frustrating. Despite your considerable efforts and inputs, you may find that the results do not match your high aspirations 
Serious consideration is warranted regarding the customer segmentation aspect of your business model. Without a focused approach towards specific customer segments, resources and efforts risk being wasted. While you may excel at creating value, appropriating that value hinges on identifying the right segments and tailoring communication accordingly. We encourage you to reach out to Transganization for comprehensive consultation on this matter. 
EOD;
        } elseif (11 >= $category_points) {
            $remark = <<<EOD
There is possibility of opportunity loss or leaving lot of revenue on table.
It appears that there may be gaps in your alignment with the marketplace. This could stem from either a misalignment in the chosen customer segment or communication that doesn't resonate with the needs and behaviors of your customers. These discrepancies likely have an impact on your sales numbers. By focusing on refining your segmentation strategy, deeply understanding your target segments, and crafting tailored communication that reflects this understanding, you can work towards bridging these gaps effectively.

If you need clarity on how to take above mentioned actions from more sustainability perspective please book cunsultation slot with transganization. 
EOD;
        } else {
            $remark = <<<EOD
You have a great market alignment and you are proactive about leveraging from this alignment.
It appears that your targeted communication is resonating well with your customers, appealing to their preferences and interests. This is likely contributing positively to your sales cycle and fostering strong customer loyalty. It's probable that many of your satisfied customers are actively referring others to your business. Overall, your alignment with the market seems commendable.

 Despite achieving excellent alignment, if the results are falling short of expectations, it suggests potential challenges in other areas like customer acquisition, Service delivery or the way you cover the potential market. Given your strong alignment with the marketplace, there's an opportunity to capitalize on even more business than your current level. We invite you to schedule a consultation with TransGanization for deeper insights and clarity on optimizing your business strategies.
EOD;
        }
    } elseif ($category_name == "Channels") {
        if (8 >= $category_points) {
            $remark = <<<EOD
Being proactive about sales is recomended
It seems the salses channel is not been paid enough attention. you could be a business with lot of demand for your product and services or you are not streching enough on your sales targets. you could be a tecnocrat in love with your product and focused on value creation,. Value appropriation could be area that is ignored. suggest more focus on reaching our customer proactively and find wayd to communicate your availabilty in the market place. If you think you need expert guidance on this very crusial aspect of business please connect with transganization for consultation....
EOD;
        } elseif (11 >= $category_points) {
            $remark = <<<EOD
Need to work on appropriate sales system.
A more proactive approach to customer acquisition is essential, as there may be opportunities you're overlooking or competitors capitalizing on your weaknesses. It's recommended to calibrate your sales channels accordingly. Additionally, conducting a customer feedback survey can provide valuable insights. If you're interested in enhancing your sales system's robustness, we invite you to schedule a consultation slot with TranGanization.
EOD;
        } else {
            $remark = <<<EOD
Your market connect is apt and relevant, This strength can be used to maximise the market and countershare.
It appears that your sales system is well-established, likely exceeding your annual sales targets. Your customers likely find it convenient to avail themselves of your products/services, resulting in fewer complaints. It's advisable to maintain these customer channels while continuously updating and enhancing them to adapt to evolving market scenarios.
However, if your results are falling short of expectations or below industry standards despite having robust sales channels, it suggests potential areas within your business model that require scrutiny. If you're interested in exploring these areas further, we encourage you to schedule a meeting with Transganization. 
EOD;
        }
    } elseif ($category_name == "Customer Relationships") {
        if (8 >= $category_points) {
            $remark = <<<EOD
Be lot more around and lot more close to your customers
Your approach to customer relationships would benefit from increased proactivity and vitality. Rather than viewing it as just another process, it should be recognized as a fundamental activity crucial for fostering repeat sales and cultivating long-term relationships with customers. Emphasizing that customers are the reason for our existence is key.

Injecting vitality into this aspect of your business can significantly contribute to achieving your overarching business objectives. If you seek further guidance on revitalizing your customer relationships, please don't hesitate to reach out to TranSsGanization. We're here to support your journey towards enhanced customer engagement and sustainable growth.
EOD;
        } elseif (11 >= $category_points) {
            $remark = <<<EOD
When customer complains, he is saying i want to be with you if you just improve a little bit, listening to customer is recommended.
It's crucial to calibrate your approach to customer relationships. Maintaining regular contact with customers and gaining insights into their post-sales expectations can facilitate adjustments and enhancements to your system. If you require assistance in this area, please feel free to schedule a complimentary consultation slot with TransGanization. We're here to help you optimize your customer relationships and drive meaningful engagement.
EOD;
        } else {
            $remark = <<<EOD
Your customer relationship seems to be foundation of your success on customer side, you can leverage on this to expnad your business exponentially
Your customer relationship management is commendable, and your customers hold your company in high regard. This proactive approach undoubtedly contributes to building a strong goodwill in the market, positioning your enterprise as highly valuable. It's essential to continue enhancing your customer relationship system to ensure its relevance and effectiveness.

However, if despite your exceptional customer relationships, you find that your strategic goals remain unmet, it may be prudent to explore other operational areas beyond customer interface. Should you wish to delve into additional areas for improvement, we invite you to connect with Transganization for expert consultation and guidance.
EOD;
        }
    } elseif ($category_name == "Revenue Streams") {
        if (8 >= $category_points) {
            $remark = <<<EOD
While things may be functioning adequately today, if the current situation persists, you could find yourself navigating through increasingly unfavorable business circumstances.
It appears to be a pivotal area for improvement that your organization should consider exploring. Beyond product sales, exploring revenue models such as subscriptions, rentals, licensing, AMC's, and consumables could pave the way for new and sustainable streams of income. The Transganization team stands ready to assist you in exploring these avenues and other initiatives aimed at enhancing your revenue strategy. Please don't hesitate to book your meeting slot to discuss further."
EOD;
        } elseif (11 >= $category_points) {
            $remark = <<<EOD
There is need for more serious attainsion on strenthening revenue streams 
"It's crucial to dedicate more attention to cultivating multiple streams of revenue, and it's equally important to strengthen existing ones by enhancing their predictability and sustainability. Your personal involvement or establishing a focused group can significantly contribute to this effort. Consistent tracking and comparison among the various revenue streams will offer valuable insights and clarity.

If you're committed to building and fortifying multiple revenue streams, we invite you to book a consultation with Transganization. Together, we can explore strategies to optimize your revenue diversification and achieve your business 
EOD;
        } else {
            $remark = <<<EOD
You are in a position to invest in future of the company and has potential to be leading player in your market
"Congratulations on cultivating multiple revenue streams within your business; it's a testament to its resilience and strength. Diversifying your revenue sources not only fortifies your business model but also positions you well for future investments and staying ahead of the curve. It's imperative to focus on enhancing the sustainability of these streams.

However, if despite having multiple revenue streams, your business finds itself unable to invest in the future or you're dissatisfied with its performance, it might be time to explore other avenues. Engaging in a transformative analysis can help identify areas of concern and chart a path forward. Please consider booking a meeting slot to delve deeper into potential strategies and solutions."
EOD;
        }
    } elseif ($category_name == "Key Resources") {
        if (2 >= $category_points) {
            $remark = <<<EOD
business model may loose relevance to market 
Understanding the significance of identifying and nurturing key resources is crucial for your business growth. Lack of clarity on your business model may contribute to overlooking this vital aspect. It is recommended that you deepen your understanding of your business model. Transganization is well-equipped to assist you in this endeavor. Please don't hesitate to schedule an appointment to explore how we can 
EOD;
        } elseif (4 >= $category_points) {
            $remark = <<<EOD
More practive approach to this area is recomended
Lack of insight into Key resources can result in misguided investments in capability development, potentially preventing the creation of differentiation or competitive advantage, ultimately leading to a loss of market edge over time. Such oversight can weaken your business model significantly. It is strongly advised to reconsider and diligently identify key resources within your business. We invite you to schedule a consultation meeting with Transganization for further clarification and strategic planning."
EOD;
        } else {
            $remark = <<<EOD
you are doing well in this area and keep doing the good work
"Understanding and prioritizing key resources is paramount for ensuring the sustainability and scalability of your business, and it's commendable that you're already excelling in this regard. The robustness of these resources directly influences your organization's capabilities, We suggest establishing formal mechanism to preserve and enhance them.

Should you find that despite your focus on these resources, organizational capabilities aren't meeting expectations, please don't hesitate to reach out to Transganization for a consultation meeting. We're here to offer support and strategic insights to help elevate your business to new heights."
EOD;
        }
    } elseif ($category_name == "Key Activities") {
        if (2 >= $category_points) {
            $remark = <<<EOD
you migh be facing frustrations in getting right things done or getting things done in right ways. 
you migh be putting lot of efforts to generate little impact. lot of activities and less accomplishments. You will have to transform from activity orientation to Accomplishment orientation. Start managing by objectives and not by activities. You migh be in Busyness
EOD;
        } elseif (4 >= $category_points) {
            $remark = <<<EOD
for sustained success reorganizing priorities is recomended.
"It's crucial to examine your business strategy and identify key activities, prioritizing them accordingly. If prioritization poses challenges, consider delving into the following aspects:

Streamlining operations through processes
Delegating non-key activities
Monitoring personnel performance
Decluttering your mind
Each of these elements provides insights into time allocation and identifies non-performing resources, ultimately aiding in the focus on key activities. Should you seek assistance in navigating these areas, we encourage you to schedule a consultation meeting with Transganization."
EOD;
        } else {
            $remark = <<<EOD
Your understanding of key activities must be providing you edge over compitetion
Recognizing and effectively implementing key activities is pivotal for enhancing organizational capabilities, and it's commendable that you excel in this area. Such proficiency should translate into operational excellence, ultimately driving profitability. However, it's important to acknowledge that while you may understand the significance of key activities, your team may prioritize differently at times.

By demonstrating the advantages of focusing on key activities, you can foster alignment with your business model, promoting synergy within your team. Even with a dedicated focus on key activities, if operations remain suboptimal, it may be necessary to scrutinize processes and systemic issues.

Should you seek further insights into cultivating an effective and efficient business, we invite you to explore consultation opportunities with TransGanization."
EOD;
        }
    } elseif ($category_name == "Cost Structure") {
        if (2 >= $category_points) {
            $remark = <<<EOD
Even if is difficult or you dont like it, "you have to do what you have to do so that you can do what you want to do"
"One of the most significant blind spots an entrepreneur can face is lacking clarity or harboring fear about numbers. While entrepreneurs may not be expected to grasp statutory accounting intricacies, understanding management accounting is imperative for business sustainability. Your acknowledgment highlights the importance of gaining clarity on management accounting principles.

We invite you to connect with Transganization for consultation, where we can provide guidance and support in enhancing your understanding of management accounting practices, empowering you to make informed decisions and sustain your business effectively."
EOD;
        } elseif (4 >= $category_points) {
            $remark = <<<EOD
Not having control over cost is not having control on business.
"Difficulty in structuring costs or maintaining cost ratios indicates the volatility of the market in which your business operates. Sustained volatility poses a significant threat to business sustainability. Businesses navigating such uncertain environments often require transformative interventions to adapt and thrive.

If you're prepared to enact changes to regain control and stability in your business, we encourage you to connect with Transganization for consultation. Together, we can explore strategies to mitigate market volatility and ensure your business's long-term 
EOD;
        } else {
            $remark = <<<EOD
Having and monitoring cost structure will ensure sustainability of your business
"Maintaining clarity on your cost structure is invaluable as it empowers you to manage profitability effectively. A profitable business enjoys sustainability over the long term, and by mastering the intricacies of cost structure, you've secured a vital aspect of business longevity.

However, despite having clarity and actively monitoring cost elements, if your business fails to achieve expected profits or struggles to control margins in certain products or services, there may be underlying blind spots that require attention. These blind spots could potentially result in significant consequences.

We encourage you to reach out to Transganization to schedule a consultation meeting, where we can assist in identifying and addressing these challenges."
EOD;
        }
    }
    // array_push($json_array['remarks'], array('category' => $category_name, 'remark' => $remark));
    return $remark;
}
