/* $Id$ */
/*
 * Six degrees of Wikipedia: JNI client interface.
 * This source code is released into the public domain.
 */

#include <string>
#include <vector>

#include <jni.h>

#include "linksc.h"

#include "org_wikimedia_links_linksc.h"

extern "C" JNIEXPORT jobjectArray JNICALL 
Java_org_wikimedia_links_linksc_findPath (JNIEnv *env, jobject, jstring jfrom, jstring jto, jboolean ignore_dates)
{
	std::string from, to;
	from = env->GetStringUTFChars(jfrom, 0);
	env->ReleaseStringUTFChars(jfrom, 0);
	to = env->GetStringUTFChars(jto, 0);
	env->ReleaseStringUTFChars(jto, 0);

	std::vector<std::string> result;
	jclass error;
	jobjectArray resultarr;
	int status = linksc_findpath(result, from, to, ignore_dates);
	const char *errorstr = "An unknown error occured";
	switch (status) {
	case 0: errorstr = "Source article does not exist."; break;
	case 1: errorstr = "Target article does not exist."; break;
	case 3: errorstr = "Could not connect to links server."; break;
	}

	if (status < 2 || status == 3) {
		resultarr = (jobjectArray) env->NewObjectArray(0,
				env->FindClass("java/lang/String"), env->NewStringUTF(""));

		error = env->FindClass("org/wikimedia/links/ErrorException");
		env->ThrowNew(error, errorstr);
		return resultarr;
	}

	resultarr = (jobjectArray) env->NewObjectArray(result.size(),
			env->FindClass("java/lang/String"), env->NewStringUTF(""));

	for (std::size_t i = 0; i < result.size(); ++i) {
		env->SetObjectArrayElement(resultarr, i, env->NewStringUTF(result[i].c_str()));
	}
	return resultarr;	
}

