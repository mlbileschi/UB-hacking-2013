import java.util.ArrayList;
import java.util.Collection;
import java.util.Iterator;

import org.jsoup.Jsoup;
import org.jsoup.nodes.Document;
import org.jsoup.nodes.Element;
import org.jsoup.select.Elements;


public class UniversityIterator {

	public static Collection<Rating> getUniversityRatings(String sid)
	{
		Collection<Rating> universityRatings = new ArrayList<Rating>();
		
		Document universityMainPage = null;
		int pageNum = 5;
		while(true)
		{
			// TODO: get JSP ID from URL
			String URLbase = "http://www.ratemyprofessors.com/SelectTeacher.jsp?sid=" + sid;
			String pageNumStr = "&pageNo="+pageNum;
			String curURL = (URLbase + pageNumStr); 

			try{
				universityMainPage = Jsoup.connect(curURL).get();
			} catch(Exception e)
			{
				// Expected 500 exception
				break;
			}

			Element ratingTableElement = universityMainPage.getElementsByAttributeValue("id", "ratingTable").iterator().next();
			Elements professorElements = ratingTableElement.getElementsByAttributeValue("class", "entry even vertical-center");
			professorElements.addAll(ratingTableElement.getElementsByAttributeValue("class", "entry odd vertical-center"));
			
			Iterator<Element> professorItr = professorElements.iterator();
			while(professorItr.hasNext())
			{
				Element curProf = professorItr.next();
				String profRatingCount = curProf.getElementsByAttributeValue("class", "profRatings").iterator().next().text();
				
				if(!profRatingCount.equals("0"))
				{
					Element profElement = curProf.getElementsByAttributeValue("class", "profName").iterator().next();
					String tid = profElement.toString().split("\"")[3].split("=")[1];
					universityRatings.addAll(RatingParser.getAllProfessorRatings(tid));
				}
			}	
			
			pageNum++;
		}
		return universityRatings;
	}
}
