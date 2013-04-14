
public class TestScript {
	
	/**
	 * args[0] = ";" separated school IDs to be 
	 * 
RIT
807
Bonaventure
832
Stanford
953
Princeton
780
Arizona State
45

	 * @param args
	 */
	public static void main(String[] args)
	{
		String[] schoolIDs = {"45"};
		for(int schoolIDLoc=0; schoolIDLoc<schoolIDs.length; schoolIDLoc++)
		{
			System.out.println("Working on school " + schoolIDs[schoolIDLoc] + "...");
			UniversityIterator.getUniversityRatings(schoolIDs[schoolIDLoc]);
		}
	}

}
