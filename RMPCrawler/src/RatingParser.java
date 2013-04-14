import java.io.BufferedWriter;
import java.io.FileWriter;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;


public class RatingParser {
	
	private static BufferedWriter ratingWriter;
	
	public static void initializeWriter(String fileName)
	{
		try {
			ratingWriter = new BufferedWriter(new FileWriter(fileName));
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
	
	public static void closeWriter()
	{
		try {
			ratingWriter.close();
		} catch (IOException e) {
			e.printStackTrace();
		}
	}
	
	public static void getAllProfessorRatings(String tid)
	{		
		Document rmpPage = null;
		int pageNum = 1;
		while(true)
		{
			// TODO: get JSP ID from URL
			String URLbase = "http://www.ratemyprofessors.com/ShowRatings.jsp?tid=" + tid;
			String pageNumStr = "&pageNo="+pageNum;
			String curURL = (URLbase + pageNumStr); 

			try{
				rmpPage = Jsoup.connect(curURL).get();
			} catch(Exception e)
			{
				// Expected 500 exception
				break;
			}
//			if(possibleErr.equals("Invalid page number"))//error on page
//			{
//				break;
//			}

			RatingParser.getRatings(rmpPage, tid);
			pageNum++;
		}
	}
	
	public static void getRatings(Document rmpPage, String tid)
	{		
		if(rmpPage.getElementsByAttributeValueContaining("style", "color:red").text().equals("All fields marked with an * are required."))
		{
			return;
		}
		
		// Prof name
		String profName = rmpPage.getElementsByAttributeValue("id", "profName").iterator().next().text();
		
		// Prof university, prof department
		Element profInfoElement = rmpPage.getElementsByAttributeValue("id", "profInfo").iterator().next();
		String profUniversity = profInfoElement.getElementsByTag("a").iterator().next().text();
		 
		Iterator<Element> strongItr = profInfoElement.getElementsByTag("strong").iterator();
		strongItr.next();
		strongItr.next();
		String profDepartment = strongItr.next().text();
		
		// Prof overall quality
		Element scoreCardElement = rmpPage.getElementsByAttributeValue("id", "scoreCard").iterator().next();
		String profQuality = scoreCardElement.getElementsByAttributeValue("id", "quality").iterator().next().getElementsByTag("strong").text();
		
		
		Elements allElements = rmpPage.getElementsByAttributeValue("class", "entry odd");
		Elements evenElements = rmpPage.getElementsByAttributeValue("class", "entry even");
		allElements.addAll(evenElements);
		Iterator<Element> elementItr = allElements.iterator();
		while(elementItr.hasNext())
		{
			Element curRecord = elementItr.next();
			Rating curRating = new Rating(profName, profUniversity, profDepartment, profQuality, tid);
			
		    // Date
			Iterator<Element> dateItr = curRecord.getElementsByAttributeValue("class", "date").iterator();
			if(dateItr.hasNext())
			{
				curRating.timeStamp = dateItr.next().text();
			}
			
			// Class
			Iterator<Element> classItr = curRecord.getElementsByAttributeValue("class", "class").iterator();
			if(classItr.hasNext())
			{
				curRating.setCourseNum(classItr.next().text().replaceAll("[^\\d]", ""));
			}
			
			// Quality, easiness, helpfulness, interest, clarity
			Iterator<Element> ratingComponentItr = curRecord.getElementsByAttributeValue("class", "rating").iterator().next().getElementsByTag("p").iterator();
			while(ratingComponentItr.hasNext())
			{
				Element ratingComponentElement = ratingComponentItr.next();
				String className = ratingComponentElement.className();
				if(className.toLowerCase().contains("quality"))
				{
					curRating.goodPoorAvgQual = ratingComponentElement.text();
				}else if(className.toLowerCase().contains("easy")){
					curRating.easiness = ratingComponentElement.text().replaceAll("[^\\d]", "");
				}else if(className.toLowerCase().contains("helpful")){
					curRating.helpfulness = ratingComponentElement.text().replaceAll("[^\\d]", "");
				}else if(className.toLowerCase().contains("clarity")){
					curRating.clarity = ratingComponentElement.text().replaceAll("[^\\d]", "");
				}else if(className.toLowerCase().contains("interest")){
					curRating.raterInterest = ratingComponentElement.text().replaceAll("[^\\d]", "");
				}
			}
			
			// Comment
			curRating.comment = curRecord.getElementsByAttributeValue("class", "commentText").iterator().next().text().replaceAll("\\t", " ");
			try {
				ratingWriter.write(curRating.toString());
			} catch (IOException e) {
				e.printStackTrace();
			}
		}
	}

}
